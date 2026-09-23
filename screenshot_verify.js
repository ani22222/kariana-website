const puppeteer = require('puppeteer');
const path = require('path');
const fs = require('fs');

process.stdout.on('error', function(err) { if (err.code === 'EPIPE') process.exit(0); });
process.stderr.on('error', function(err) { if (err.code === 'EPIPE') process.exit(0); });

const ARTIFACT_DIR = 'C:\\Users\\UseR\\.gemini\\antigravity\\brain\\b5d31c4a-85e9-4609-b28a-3786eed9a1a3';
const STORAGE_LOGS = path.join(__dirname, 'storage', 'logs');
if (!fs.existsSync(STORAGE_LOGS)) {
    fs.mkdirSync(STORAGE_LOGS, { recursive: true });
}

(async () => {
    let browser;
    try {
        browser = await puppeteer.launch({
            headless: 'new',
            args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-web-security']
        });

        // Mobile screenshot (375x812 - iPhone dimensions)
        const mobilePage = await browser.newPage();
        await mobilePage.setViewport({ width: 375, height: 812, deviceScaleFactor: 2 });
        await mobilePage.goto('http://127.0.0.1:8015', { waitUntil: 'networkidle2', timeout: 30000 });
        await new Promise(r => setTimeout(r, 3000));
        
        const mobilePathArtifact = path.join(ARTIFACT_DIR, 'verify_mobile.png');
        const mobilePathStorage = path.join(STORAGE_LOGS, 'verify_mobile.png');
        await mobilePage.screenshot({ path: mobilePathArtifact, fullPage: false });
        try { fs.copyFileSync(mobilePathArtifact, mobilePathStorage); } catch(e) {}
        console.log('Mobile screenshot captured');

        // Scroll to #mainContentSection and take screenshot
        await mobilePage.evaluate(() => {
            const el = document.getElementById('mainContentSection');
            if (el) el.scrollIntoView();
        });
        await new Promise(r => setTimeout(r, 1000));
        const mobileScrollPath = path.join(STORAGE_LOGS, 'verify_mobile_scrolled.png');
        await mobilePage.screenshot({ path: mobileScrollPath, fullPage: false });
        console.log('Mobile scrolled screenshot captured');

        // Desktop screenshot (1440x900)
        const desktopPage = await browser.newPage();
        await desktopPage.setViewport({ width: 1440, height: 900, deviceScaleFactor: 1 });
        await desktopPage.goto('http://127.0.0.1:8015', { waitUntil: 'networkidle2', timeout: 30000 });
        await new Promise(r => setTimeout(r, 3000));
        
        const desktopPathArtifact = path.join(ARTIFACT_DIR, 'verify_desktop.png');
        const desktopPathStorage = path.join(STORAGE_LOGS, 'verify_desktop.png');
        await desktopPage.screenshot({ path: desktopPathArtifact, fullPage: false });
        try { fs.copyFileSync(desktopPathArtifact, desktopPathStorage); } catch(e) {}
        console.log('Desktop screenshot captured');

        // Scroll desktop to #mainContentSection
        await desktopPage.evaluate(() => {
            const el = document.getElementById('mainContentSection');
            if (el) el.scrollIntoView();
        });
        await new Promise(r => setTimeout(r, 1000));
        const desktopScrollPath = path.join(STORAGE_LOGS, 'verify_desktop_scrolled.png');
        await desktopPage.screenshot({ path: desktopScrollPath, fullPage: false });
        console.log('Desktop scrolled screenshot captured');

    } catch(e) {
        console.error('Puppeteer error:', e);
    } finally {
        if (browser) {
            try { await browser.close(); } catch(e) {}
        }
    }
})();
