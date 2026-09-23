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

function saveDual(filename, srcPath) {
    const artPath = path.join(ARTIFACT_DIR, filename);
    const storPath = path.join(STORAGE_LOGS, filename);
    try { fs.copyFileSync(srcPath, artPath); } catch(e) {}
    try { fs.copyFileSync(srcPath, storPath); } catch(e) {}
}

(async () => {
    let browser;
    try {
        browser = await puppeteer.launch({
            headless: 'new',
            args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-web-security']
        });

        // 1. DESKTOP SESSION (1440x900)
        const desktopPage = await browser.newPage();
        await desktopPage.setViewport({ width: 1440, height: 900, deviceScaleFactor: 1 });
        await desktopPage.goto('http://127.0.0.1:8015', { waitUntil: 'networkidle2', timeout: 30000 });
        await new Promise(r => setTimeout(r, 2000));

        // A. Desktop Light Mode Scrolled View
        await desktopPage.evaluate(() => {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('kariana_theme', 'light');
            const el = document.getElementById('mainContentSection');
            if (el) el.scrollIntoView();
        });
        await new Promise(r => setTimeout(r, 1000));
        const deskLightPath = path.join(STORAGE_LOGS, 'desktop_scrolled_light.png');
        await desktopPage.screenshot({ path: deskLightPath, fullPage: false });
        saveDual('desktop_scrolled_light.png', deskLightPath);
        console.log('Desktop Light Scrolled Captured');

        // B. Desktop Dark Mode Scrolled View
        await desktopPage.evaluate(() => {
            document.documentElement.classList.add('dark');
            localStorage.setItem('kariana_theme', 'dark');
            const el = document.getElementById('mainContentSection');
            if (el) el.scrollIntoView();
        });
        await new Promise(r => setTimeout(r, 1000));
        const deskDarkPath = path.join(STORAGE_LOGS, 'desktop_scrolled_dark.png');
        await desktopPage.screenshot({ path: deskDarkPath, fullPage: false });
        saveDual('desktop_scrolled_dark.png', deskDarkPath);
        saveDual('verify_desktop_scrolled.png', deskDarkPath);
        console.log('Desktop Dark Scrolled Captured');

        // C. Desktop App Hub Modal
        await desktopPage.evaluate(() => {
            const btn = document.getElementById('headerAppInstallBtn');
            if (btn) btn.click();
        });
        await new Promise(r => setTimeout(r, 1000));
        const deskModalPath = path.join(STORAGE_LOGS, 'app_hub_desktop.png');
        await desktopPage.screenshot({ path: deskModalPath, fullPage: false });
        saveDual('app_hub_desktop.png', deskModalPath);
        console.log('Desktop App Hub Modal Captured');

        // 2. MOBILE SESSION (375x812 iPhone)
        const mobilePage = await browser.newPage();
        await mobilePage.setViewport({ width: 375, height: 812, deviceScaleFactor: 2 });
        await mobilePage.goto('http://127.0.0.1:8015', { waitUntil: 'networkidle2', timeout: 30000 });
        await new Promise(r => setTimeout(r, 2000));

        // A. Mobile Light Mode Scrolled View
        await mobilePage.evaluate(() => {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('kariana_theme', 'light');
            const el = document.getElementById('mainContentSection');
            if (el) el.scrollIntoView();
        });
        await new Promise(r => setTimeout(r, 1000));
        const mobLightPath = path.join(STORAGE_LOGS, 'mobile_scrolled_light.png');
        await mobilePage.screenshot({ path: mobLightPath, fullPage: false });
        saveDual('mobile_scrolled_light.png', mobLightPath);
        console.log('Mobile Light Scrolled Captured');

        // B. Mobile Dark Mode Scrolled View
        await mobilePage.evaluate(() => {
            document.documentElement.classList.add('dark');
            localStorage.setItem('kariana_theme', 'dark');
            const el = document.getElementById('mainContentSection');
            if (el) el.scrollIntoView();
        });
        await new Promise(r => setTimeout(r, 1000));
        const mobDarkPath = path.join(STORAGE_LOGS, 'mobile_scrolled_dark.png');
        await mobilePage.screenshot({ path: mobDarkPath, fullPage: false });
        saveDual('mobile_scrolled_dark.png', mobDarkPath);
        saveDual('verify_mobile_scrolled.png', mobDarkPath);
        console.log('Mobile Dark Scrolled Captured');

        // C. Mobile App Hub Modal
        await mobilePage.evaluate(() => {
            const btn = document.getElementById('headerAppInstallBtn');
            if (btn) btn.click();
        });
        await new Promise(r => setTimeout(r, 1000));
        const mobModalPath = path.join(STORAGE_LOGS, 'app_hub_mobile.png');
        await mobilePage.screenshot({ path: mobModalPath, fullPage: false });
        saveDual('app_hub_mobile.png', mobModalPath);
        console.log('Mobile App Hub Modal Captured');

    } catch(e) {
        console.error('Puppeteer error:', e);
    } finally {
        if (browser) {
            try { await browser.close(); } catch(e) {}
        }
    }
})();
