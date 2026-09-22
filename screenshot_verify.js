const puppeteer = require('puppeteer');
const path = require('path');

const ARTIFACT_DIR = 'C:\\Users\\UseR\\.gemini\\antigravity\\brain\\b5d31c4a-85e9-4609-b28a-3786eed9a1a3';

(async () => {
    const browser = await puppeteer.launch({
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-web-security']
    });

    try {
        // Mobile screenshot (375x812 - iPhone dimensions)
        console.log('Taking mobile screenshot...');
        const mobilePage = await browser.newPage();
        await mobilePage.setViewport({ width: 375, height: 812, deviceScaleFactor: 2 });
        await mobilePage.goto('http://localhost:8015', { waitUntil: 'networkidle2', timeout: 20000 });
        await new Promise(r => setTimeout(r, 2000));
        const mobilePath = path.join(ARTIFACT_DIR, 'verify_mobile.png');
        await mobilePage.screenshot({ path: mobilePath, fullPage: false });
        console.log('Mobile screenshot saved:', mobilePath);

        // Desktop screenshot (1440x900)
        console.log('Taking desktop screenshot...');
        const desktopPage = await browser.newPage();
        await desktopPage.setViewport({ width: 1440, height: 900, deviceScaleFactor: 1 });
        await desktopPage.goto('http://localhost:8015', { waitUntil: 'networkidle2', timeout: 20000 });
        await new Promise(r => setTimeout(r, 2000));
        const desktopPath = path.join(ARTIFACT_DIR, 'verify_desktop.png');
        await desktopPage.screenshot({ path: desktopPath, fullPage: false });
        console.log('Desktop screenshot saved:', desktopPath);

    } catch(e) {
        console.error('Error:', e.message);
    }

    await browser.close();
    console.log('Done!');
})();
