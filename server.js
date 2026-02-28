import express from 'express';
import puppeteer from 'puppeteer';

const app = express();

app.use(express.json({ limit: '50mb' }));

app.post('/generar-pdf', async (req, res) => {
    try {
        const { html, filename } = req.body;

        const browser = await puppeteer.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });

        const page = await browser.newPage();
        await page.setContent(html, {
            waitUntil: 'domcontentloaded',
            timeout: 60000
        });

        const pdfBuffer = await page.pdf({
            format: 'A4',
            margin: { top: '0.5cm', bottom: '0.5cm', left: '0.5cm', right: '0.5cm' },
            printBackground: true
        });

        await browser.close();

        res.setHeader('Content-Type', 'application/pdf');
        res.setHeader('Content-Disposition', `attachment; filename="${filename}"`);
        res.send(pdfBuffer);

    } catch (error) {
        console.error('Error:', error);
        res.status(500).json({ error: error.message });
    }
});

const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Servidor Puppeteer en http://localhost:${PORT}`);
});
