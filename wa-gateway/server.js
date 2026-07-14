const express = require('express');
const cors = require('cors');
const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode');
const fs = require('fs');
const path = require('path');

const app = express();
app.use(cors());
app.use(express.json());

let qrData = null;
let isReady = false;

// --- AUTO HEALING: Hapus Cache yang berpotensi Korup ---
const cachePath = path.join(__dirname, '.wwebjs_cache');
if (fs.existsSync(cachePath)) {
    try {
        fs.rmSync(cachePath, { recursive: true, force: true });
        console.log('[Auto-Healing] Cache wwebjs lama berhasil dibersihkan.');
    } catch (err) {
        console.error('[Auto-Healing] Gagal membersihkan cache:', err);
    }
}
// --------------------------------------------------------

// Inisialisasi WhatsApp Client
const client = new Client({
    authStrategy: new LocalAuth({ clientId: 'spp-client' }),
    puppeteer: {
        executablePath: process.platform === 'win32' 
            ? 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe' 
            : '/usr/bin/google-chrome-stable', // Path untuk Ubuntu/Linux
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu',
            '--disable-background-timer-throttling',
            '--disable-backgrounding-occluded-windows',
            '--disable-renderer-backgrounding'
        ]
    },
    webVersionCache: {
        type: 'none'
    }
});

client.on('qr', (qr) => {
    console.log('QR Code Received!');
    qrData = qr;
});

client.on('ready', () => {
    console.log('WhatsApp Client is ready!');
    isReady = true;
    qrData = null; // Hapus QR jika sudah connect
});

client.on('disconnected', (reason) => {
    console.log('WhatsApp Client disconnected', reason);
    isReady = false;
    qrData = null;
});

client.initialize();

// Helper untuk format nomor ke 628xxxxxx@c.us
const formatPhone = (phone) => {
    let formatted = phone.replace(/\D/g, '');
    if (formatted.startsWith('0')) {
        formatted = '62' + formatted.substr(1);
    }
    if (!formatted.endsWith('@c.us')) {
        formatted += '@c.us';
    }
    return formatted;
};

// --- ENDPOINTS ---

app.get('/status', async (req, res) => {
    if (isReady) {
        return res.json({ status: 'ready', qr: null });
    }
    
    if (qrData) {
        try {
            const qrImage = await qrcode.toDataURL(qrData);
            return res.json({ status: 'qr', qr: qrImage });
        } catch (err) {
            return res.status(500).json({ status: 'error', message: 'Failed to generate QR' });
        }
    }

    return res.json({ status: 'loading', qr: null });
});

// Endpoint pengiriman (dengan delay agar tidak terbaca spam)
app.post('/send-bulk', async (req, res) => {
    if (!isReady) {
        return res.status(400).json({ success: false, message: 'WhatsApp is not ready' });
    }

    const messages = req.body.messages; // [{phone: '081...', message: 'Halo...'}, ...]
    if (!Array.isArray(messages)) {
        return res.status(400).json({ success: false, message: 'Invalid data format' });
    }

    // Kita return response duluan agar browser laravel tidak loading muter-muter
    res.json({ success: true, message: `Mengantre ${messages.length} pesan. Pengiriman sedang berjalan di background.` });

    // Hitung delay sekali di luar loop
    const minMs = req.body.delayMin ? parseInt(req.body.delayMin) * 1000 : 3000;
    const maxMs = req.body.delayMax ? parseInt(req.body.delayMax) * 1000 : 7000;
    const finalMax = maxMs >= minMs ? maxMs : minMs;

    // Loop pengiriman asinkron di background dengan delay acak (anti spam)
    for (let i = 0; i < messages.length; i++) {
        const item = messages[i];

        // Delay SEBELUM setiap pengiriman (termasuk pesan pertama)
        const delayMs = Math.floor(Math.random() * (finalMax - minMs + 1) + minMs);
        console.log(`[Delay] Menunggu ${delayMs}ms sebelum kirim ke ${item.phone}...`);
        await new Promise(resolve => setTimeout(resolve, delayMs));

        try {
            const target = formatPhone(item.phone);
            
            if (item.media && fs.existsSync(item.media)) {
                const media = MessageMedia.fromFilePath(item.media);
                // For files, the caption is sent in the 'caption' option
                await client.sendMessage(target, media, { caption: item.message });
            } else {
                await client.sendMessage(target, item.message);
            }
            
            console.log(`[Success] Sent to ${item.phone}`);
        } catch (error) {
            console.error(`[Error] Failed to send to ${item.phone}:`, error);
        }
    }
});

const PORT = 3000;
app.listen(PORT, () => {
    console.log(`WA Gateway Server running on http://localhost:${PORT}`);
});
