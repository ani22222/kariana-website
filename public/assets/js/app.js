/**
 * Kariana Quran Portal - Client Application Script
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Dynamic Bengali Date Generator
    const dateDisplay = document.getElementById('bangla-date-display');
    if (dateDisplay) {
        const bnMonths = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
        const bnDays = ['রবিবার', 'সোমবার', 'মঙ্গলবার', 'বুধবার', 'বৃহস্পতিবার', 'শুক্রবার', 'শনিবার'];
        const toBnNumber = (str) => {
            const en = ['0','1','2','3','4','5','6','7','8','9'];
            const bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
            return String(str).replace(/[0-9]/g, match => bn[en.indexOf(match)]);
        };

        const now = new Date();
        const dayName = bnDays[now.getDay()];
        const dateNum = toBnNumber(now.getDate());
        const monthName = bnMonths[now.getMonth()];
        const yearNum = toBnNumber(now.getFullYear());

        dateDisplay.textContent = `${dayName}, ${dateNum} ${monthName} ${yearNum}`;
    }

    // 2. Mobile Nav Active State
    console.log('Kariana Quran Portal Client Engine Initialized.');
});
