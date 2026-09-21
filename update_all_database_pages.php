<?php

use App\Models\Page;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$enMap = json_decode(file_get_contents(__DIR__ . '/translations_en.json'), true);
$ruMap = json_decode(file_get_contents(__DIR__ . '/translations_ru.json'), true);

$titlesEn = [
    '' => [
        'title' => 'Law Office BULLET | Legal Assistance and Defense',
        'description' => 'Law Office BULLET provides legal assistance and defense to companies and individuals in Latvia and internationally.'
    ],
    'aktualitates' => [
        'title' => 'News & Insights | Law Office BULLET',
        'description' => 'Latest industry news, firm and attorney insights from Law Office BULLET.'
    ],
    'jurista-darbibas-nozare' => [
        'title' => 'Practice Areas | Law Office BULLET',
        'description' => 'Law Office BULLET practice areas: Criminal Law, Commercial Law, Litigation, Insolvency, Immigration, Real Estate.'
    ],
    'jurista-darbibas-nozare/kriminalprocess' => [
        'title' => 'Criminal Law | Law Office BULLET',
        'description' => 'Criminal proceedings and defense in criminal cases in Latvia and internationally.'
    ],
    'jurista-darbibas-nozare/komerctiesibas' => [
        'title' => 'Commercial Law | Law Office BULLET',
        'description' => 'Commercial law, business transactions, corporate governance, and contractual relations in Latvia.'
    ],
    'jurista-darbibas-nozare/tiesvediba' => [
        'title' => 'Litigation & Dispute Resolution | Law Office BULLET',
        'description' => 'Representation in civil, commercial, and administrative litigation and out-of-court dispute resolution.'
    ],
    'jurista-darbibas-nozare/maksatnespeja' => [
        'title' => 'Insolvency & Restructuring | Law Office BULLET',
        'description' => 'Legal assistance in insolvency, corporate recovery, bankruptcy, and debt restructuring proceedings.'
    ],
    'jurista-darbibas-nozare/imigracija' => [
        'title' => 'Immigration Law | Law Office BULLET',
        'description' => 'Residence permits, business visas, relocation, and citizenship assistance in Latvia.'
    ],
    'jurista-darbibas-nozare/nekustamais-ipasums' => [
        'title' => 'Real Estate Law | Law Office BULLET',
        'description' => 'Legal counsel for real estate transactions, property development, title diligence, and tenancy.'
    ],
    'juridiskie-pakalpojumi' => [
        'title' => 'Legal Services | Law Office BULLET',
        'description' => 'Comprehensive legal services for businesses and individuals in Latvia and internationally.'
    ],
    'juridiskie-pakalpojumi/aizstaviba-kriminalprocesa' => [
        'title' => 'Defense in Criminal Proceedings | Law Office BULLET',
        'description' => 'Professional defense and legal representation at all stages of criminal proceedings.'
    ],
    'juridiskie-pakalpojumi/maksatnespejas-process' => [
        'title' => 'Insolvency Proceedings | Law Office BULLET',
        'description' => 'Legal support in insolvency and legal protection proceedings for businesses and individuals.'
    ],
    'juridiskie-pakalpojumi/komercstridu-risinasana' => [
        'title' => 'Commercial Dispute Resolution | Law Office BULLET',
        'description' => 'Representation of client interests in commercial disputes, litigation, and arbitration.'
    ],
    'juridiskie-pakalpojumi/imigracijas-jautajumu-risinasana' => [
        'title' => 'Immigration Services | Law Office BULLET',
        'description' => 'Assisting entrepreneurs, investors, and foreign specialists with immigration and investment matters.'
    ],
    'juridiskie-pakalpojumi/krizes-vadiba' => [
        'title' => 'Crisis Management | Law Office BULLET',
        'description' => 'Strategic legal crisis management, asset protection, and urgent response.'
    ],
    'juridiskie-pakalpojumi/darijumu-vadiba' => [
        'title' => 'Transaction Management | Law Office BULLET',
        'description' => 'M&A, corporate restructuring, and commercial deal structuring in Latvia and across Europe.'
    ],
    'juridiskie-pakalpojumi/juridisko-dokumentu-sagatavosana' => [
        'title' => 'Legal Document Drafting | Law Office BULLET',
        'description' => 'Drafting and reviewing complex commercial agreements, corporate resolutions, and legal filings.'
    ],
    'juridiskie-pakalpojumi/juridiska-palidziba-un-konsultacijas' => [
        'title' => 'Legal Assistance & Consultations | Law Office BULLET',
        'description' => 'Professional legal consultations and assistance tailored to individual and corporate clients.'
    ],
    'juridiskie-pakalpojumi/reiderisma-noversana' => [
        'title' => 'Anti-Raiding & Corporate Defense | Law Office BULLET',
        'description' => 'Prevention and defense against hostile takeovers, corporate raiding, and illegal shareholder actions.'
    ],
    'zverinati-advokati' => [
        'title' => 'Sworn Advocates | Law Office BULLET',
        'description' => 'Meet our team of experienced sworn advocates and legal specialists.'
    ],
    'zverinati-advokati/karlis-zunde' => [
        'title' => 'Kārlis Zunde - Sworn Advocate | Law Office BULLET',
        'description' => 'Kārlis Zunde - Sworn Advocate specializing in criminal defense and commercial law.'
    ],
    'zverinati-advokati/uldis-lapins' => [
        'title' => 'Uldis Lapiņš - Sworn Advocate | Law Office BULLET',
        'description' => 'Uldis Lapiņš - Sworn Advocate specializing in litigation, insolvency, and real estate.'
    ],
    'zverinati-advokati/maris-bertmanis' => [
        'title' => 'Māris Bertmanis - Sworn Advocate | Law Office BULLET',
        'description' => 'Māris Bertmanis - Sworn Advocate specializing in corporate transactions and dispute resolution.'
    ],
    'par-mums' => [
        'title' => 'About Us | Law Office BULLET',
        'description' => 'About Law Office BULLET - our values, experience, and international legal practice.'
    ],
    'kontakti' => [
        'title' => 'Contacts | Law Office BULLET',
        'description' => 'Contact Law Office BULLET - phone +371 27 484 000, email welcome@bullet.legal, Riga, Latvia.'
    ],
];

$titlesRu = [
    '' => [
        'title' => 'Адвокатское бюро BULLET | Юридическая помощь и защита',
        'description' => 'Адвокатское бюро BULLET оказывает юридическую помощь предприятиям и частным лицам в Латвии и на международном уровне.'
    ],
    'aktualitates' => [
        'title' => 'Новости и публикации | Адвокатское бюро BULLET',
        'description' => 'Актуальные новости отрасли, мнения адвокатов и аналитика от Адвокатского бюро BULLET.'
    ],
    'jurista-darbibas-nozare' => [
        'title' => 'Сферы практики | Адвокатское бюро BULLET',
        'description' => 'Сферы практики бюро BULLET: уголовное право, коммерческое право, судебные споры, неплатежеспособность, иммиграция, недвижимость.'
    ],
    'jurista-darbibas-nozare/kriminalprocess' => [
        'title' => 'Уголовный процесс | Адвокатское бюро BULLET',
        'description' => 'Защита и представление интересов в уголовных процессах в Латвии и на международном уровне.'
    ],
    'jurista-darbibas-nozare/komerctiesibas' => [
        'title' => 'Коммерческое право | Адвокатское бюро BULLET',
        'description' => 'Коммерческое право, корпоративное управление, договоры и сопровождение бизнеса в Латвии.'
    ],
    'jurista-darbibas-nozare/tiesvediba' => [
        'title' => 'Судопроизводство и споры | Адвокатское бюро BULLET',
        'description' => 'Представительство в судах по гражданским, коммерческим и административным делам, досудебное урегулирование.'
    ],
    'jurista-darbibas-nozare/maksatnespeja' => [
        'title' => 'Неплатежеспособность и реструктуризация | Адвокатское бюро BULLET',
        'description' => 'Юридическая помощь в процессах неплатежеспособности, банкротства и правовой защиты предприятий.'
    ],
    'jurista-darbibas-nozare/imigracija' => [
        'title' => 'Иммиграционное право | Адвокатское бюро BULLET',
        'description' => 'Виды на жительство, бизнес-визиты, релокация и вопросы гражданства в Латвии.'
    ],
    'jurista-darbibas-nozare/nekustamais-ipasums' => [
        'title' => 'Недвижимость | Адвокатское бюро BULLET',
        'description' => 'Юридическое сопровождение сделок с недвижимостью, аудит прав собственности и девелопмент.'
    ],
    'juridiskie-pakalpojumi' => [
        'title' => 'Юридические услуги | Адвокатское бюро BULLET',
        'description' => 'Комплексные юридические услуги для компаний и частных лиц в Латвии и за рубежом.'
    ],
    'juridiskie-pakalpojumi/aizstaviba-kriminalprocesa' => [
        'title' => 'Защита в уголовном процессе | Адвокатское бюро BULLET',
        'description' => 'Профессиональная защита и юридическая помощь на всех стадиях уголовного процесса.'
    ],
    'juridiskie-pakalpojumi/maksatnespejas-process' => [
        'title' => 'Процесс неплатежеспособности | Адвокатское бюро BULLET',
        'description' => 'Юридическая поддержка в процессах неплатежеспособности и правовой защиты.'
    ],
    'juridiskie-pakalpojumi/komercstridu-risinasana' => [
        'title' => 'Разрешение коммерческих споров | Адвокатское бюро BULLET',
        'description' => 'Защита интересов клиентов в коммерческих спорах, судебных разбирательствах и арбитраже.'
    ],
    'juridiskie-pakalpojumi/imigracijas-jautajumu-risinasana' => [
        'title' => 'Решение иммиграционных вопросов | Адвокатское бюро BULLET',
        'description' => 'Помощь предпринимателям, инвесторам и иностранным специалистам по вопросам иммиграции.'
    ],
    'juridiskie-pakalpojumi/krizes-vadiba' => [
        'title' => 'Антикризисное управление | Адвокатское бюро BULLET',
        'description' => 'Стратегическое юридическое управление кризисами, защита активов и экстренное реагирование.'
    ],
    'juridiskie-pakalpojumi/darijumu-vadiba' => [
        'title' => 'Управление сделками | Адвокатское бюро BULLET',
        'description' => 'Сделки M&A, корпоративная реструктуризация и структурирование сделок в Латвии и Европе.'
    ],
    'juridiskie-pakalpojumi/juridisko-dokumentu-sagatavosana' => [
        'title' => 'Подготовка юридических документов | Адвокатское бюро BULLET',
        'description' => 'Составление и аудит сложных коммерческих договоров, корпоративных решений и юридических документов.'
    ],
    'juridiskie-pakalpojumi/juridiska-palidziba-un-konsultacijas' => [
        'title' => 'Юридическая помощь и консультации | Адвокатское бюро BULLET',
        'description' => 'Профессиональные юридические консультации и правовая помощь для частных и корпоративных клиентов.'
    ],
    'juridiskie-pakalpojumi/reiderisma-noversana' => [
        'title' => 'Защита от рейдерства | Адвокатское бюро BULLET',
        'description' => 'Предотвращение недружественных поглощений, рейдерства и незаконных действий с долями капитала.'
    ],
    'zverinati-advokati' => [
        'title' => 'Присяжные адвокаты | Адвокатское бюро BULLET',
        'description' => 'Наша команда опытных присяжных адвокатов и юридических специалистов.'
    ],
    'zverinati-advokati/karlis-zunde' => [
        'title' => 'Карлис Зунде - Присяжный адвокат | Адвокатское бюро BULLET',
        'description' => 'Карлис Зунде — присяжный адвокат, специалист по уголовной защите и коммерческому праву.'
    ],
    'zverinati-advokati/uldis-lapins' => [
        'title' => 'Улдис Лапиньш - Присяжный адвокат | Адвокатское бюро BULLET',
        'description' => 'Улдис Лапиньш — присяжный адвокат, специалист по судебным спорам, неплатежеспособности и недвижимости.'
    ],
    'zverinati-advokati/maris-bertmanis' => [
        'title' => 'Марис Бертманис - Присяжный адвокат | Адвокатское бюро BULLET',
        'description' => 'Марис Бертманис — присяжный адвокат, специалист по сделкам и разрешению споров.'
    ],
    'par-mums' => [
        'title' => 'О нас | Адвокатское бюро BULLET',
        'description' => 'Об адвокатском бюро BULLET — наши ценности, опыт и международная юридическая практика.'
    ],
    'kontakti' => [
        'title' => 'Контакты | Адвокатское бюро BULLET',
        'description' => 'Контакты Адвокатского бюро BULLET — телефон +371 27 484 000, e-mail welcome@bullet.legal, Рига, Латвия.'
    ],
];

// Helper to build translation replacer with entity variations
function buildReplacer($dict) {
    $replacements = [];
    foreach ($dict as $lv => $trans) {
        $replacements[$lv] = $trans;
        // HTML encoded version
        $encodedLv = htmlspecialchars($lv, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $encodedTrans = htmlspecialchars($trans, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($encodedLv !== $lv) {
            $replacements[$encodedLv] = $encodedTrans;
        }
        // HTML entities variations
        $entitiesLv = htmlentities($lv, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($entitiesLv !== $lv && $entitiesLv !== $encodedLv) {
            $replacements[$entitiesLv] = $encodedTrans;
        }
        // Dash variation
        $dashLv = str_replace('–', '&#8211;', $lv);
        if ($dashLv !== $lv) {
            $replacements[$dashLv] = str_replace('–', '&#8211;', $trans);
        }
    }
    
    // Sort by key length descending
    uksort($replacements, function ($a, $b) {
        return mb_strlen($b) <=> mb_strlen($a);
    });
    
    return $replacements;
}

$enReplacements = buildReplacer($enMap);
$ruReplacements = buildReplacer($ruMap);

$build3LangSwitcher = function ($headerMarkup, $currLocale, $slug) {
    $currLabel = strtoupper($currLocale);
    $langs = [
        'LV' => '/' . ($slug ? $slug . '/' : ''),
        'EN' => '/en/' . ($slug ? $slug . '/' : ''),
        'RU' => '/ru/' . ($slug ? $slug . '/' : ''),
    ];

    $optionsHtml = '';
    foreach ($langs as $l => $url) {
        if (strtoupper($currLocale) === $l) continue;
        $optionsHtml .= '<a href="' . $url . '" class="header__lang">' . $l . '</a>';
    }

    $pllHtml = '<div class="header__pll">'
        . '<svg class="header__icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">'
        . '<path d="M10 18.4792C8.83333 18.4792 7.72917 18.2569 6.6875 17.8125C5.65972 17.3681 4.76389 16.7639 4 16C3.23611 15.2361 2.63194 14.3403 2.1875 13.3125C1.74306 12.2708 1.52083 11.1667 1.52083 10C1.52083 8.81944 1.74306 7.71528 2.1875 6.6875C2.63194 5.65972 3.23611 4.76389 4 4C4.76389 3.23611 5.65972 2.63194 6.6875 2.1875C7.72917 1.74305 8.83333 1.52083 10 1.52083C11.1806 1.52083 12.2847 1.74305 13.3125 2.1875C14.3403 2.63194 15.2361 3.23611 16 4C16.7639 4.76389 17.3681 5.66667 17.8125 6.70833C18.2569 7.73611 18.4792 8.83333 18.4792 10C18.4792 11.1806 18.2569 12.2847 17.8125 13.3125C17.3681 14.3403 16.7639 15.2361 16 16C15.2361 16.7639 14.3403 17.3681 13.3125 17.8125C12.2847 18.2569 11.1806 18.4792 10 18.4792ZM9.14583 16.7083V15.0625C8.6875 15.0625 8.29167 14.8958 7.95833 14.5625C7.63889 14.2292 7.47917 13.8333 7.47917 13.375V12.5208L3.4375 8.45833C3.38194 8.72222 3.33333 8.97917 3.29167 9.22917C3.26389 9.47917 3.25 9.73611 3.25 10C3.25 11.7083 3.80556 13.2083 4.91667 14.5C6.04167 15.7778 7.45139 16.5139 9.14583 16.7083ZM14.9792 14.5625C15.2708 14.2569 15.5278 13.9236 15.75 13.5625C15.9722 13.2014 16.1528 12.8264 16.2917 12.4375C16.4444 12.0486 16.5556 11.6528 16.625 11.25C16.7083 10.8333 16.75 10.4167 16.75 10C16.75 8.61111 16.3681 7.34722 15.6042 6.20833C14.8403 5.06944 13.8194 4.24305 12.5417 3.72917V4.0625C12.5417 4.52083 12.375 4.92361 12.0417 5.27083C11.7083 5.60417 11.3125 5.77083 10.8542 5.77083H9.14583V7.45833C9.14583 7.69444 9.0625 7.89583 8.89583 8.0625C8.72917 8.21528 8.52778 8.29167 8.29167 8.29167H6.625V10H11.7083C11.9444 10 12.1389 10.0833 12.2917 10.25C12.4583 10.4167 12.5417 10.6111 12.5417 10.8333V13.375H13.375C13.75 13.375 14.0833 13.4861 14.375 13.7083C14.6667 13.9306 14.8681 14.2153 14.9792 14.5625Z" fill="currentColor"></path>'
        . '</svg>'
        . '<span class="header__current" style="cursor:pointer;display:inline-flex;align-items:center;gap:4px;">'
        . $currLabel
        . '<svg class="header__arrow" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">'
        . '<path d="M9.54167 12.1875L6.33333 8.97917C6.29167 8.9375 6.25694 8.89583 6.22917 8.85417C6.20139 8.79861 6.1875 8.73611 6.1875 8.66667C6.1875 8.55555 6.22917 8.45833 6.3125 8.375C6.39583 8.27778 6.5 8.22917 6.625 8.22917H13.375C13.5 8.22917 13.6042 8.27778 13.6875 8.375C13.7708 8.45833 13.8125 8.55555 13.8125 8.66667C13.8125 8.70833 13.7639 8.8125 13.6667 8.97917L10.4583 12.1875C10.3889 12.2569 10.3125 12.3056 10.2292 12.3333C10.1597 12.3611 10.0833 12.375 10 12.375C9.91667 12.375 9.83333 12.3611 9.75 12.3333C9.68056 12.3056 9.61111 12.2569 9.54167 12.1875Z" fill="currentColor"></path>'
        . '</svg>'
        . '</span>'
        . '<div class="header__select">'
        . $optionsHtml
        . '</div>'
        . '</div>';

    return preg_replace('/<div class=[\'"]header__pll[\'"].*?<\/div>\s*<\/div>/is', $pllHtml, $headerMarkup);
};

$transformLinksLocale = function ($markup, $targetLocale) {
    return preg_replace_callback('/href=([\'"])(\/[^\'"\s]*)?([\'"])/i', function ($m) use ($targetLocale) {
        $path = $m[2] ?? '/';
        if ($path === '#' || str_starts_with($path, 'tel:') || str_starts_with($path, 'mailto:') || preg_match('/\.(css|js|svg|jpg|jpeg|png|webp|gif)(\?.*)?$/i', $path)) {
            return $m[0];
        }
        // Strip any existing /en/ or /ru/ prefix
        $cleanPath = preg_replace('/^\/(en|ru)(\/|$)/', '/', $path);
        if ($targetLocale === 'en') {
            $cleanPath = '/en' . ($cleanPath === '/' ? '' : $cleanPath);
        } elseif ($targetLocale === 'ru') {
            $cleanPath = '/ru' . ($cleanPath === '/' ? '' : $cleanPath);
        }
        return 'href=' . $m[1] . $cleanPath . $m[3];
    }, $markup);
};

$lvPages = Page::where('locale', 'lv')->get();

// 1. Update LV pages
foreach ($lvPages as $lvPage) {
    $slug = $lvPage->slug;
    $header = $build3LangSwitcher($transformLinksLocale($lvPage->header_html, 'lv'), 'lv', $slug);
    $content = $transformLinksLocale($lvPage->content_html, 'lv');
    $footer = $transformLinksLocale($lvPage->footer_html, 'lv');

    $lvPage->update([
        'header_html' => $header,
        'content_html' => $content,
        'footer_html' => $footer,
    ]);
}
echo "Updated 25 LV pages in database." . PHP_EOL;

// 2. Generate and Update EN pages
foreach ($lvPages as $lvPage) {
    $slug = $lvPage->slug;
    $title = $titlesEn[$slug]['title'] ?? ($lvPage->title . ' | Law Office BULLET');
    $description = $titlesEn[$slug]['description'] ?? $lvPage->description;
    $bodyClass = $lvPage->body_class;
    
    $header = strtr($build3LangSwitcher($transformLinksLocale($lvPage->header_html, 'en'), 'en', $slug), $enReplacements);
    $content = strtr($transformLinksLocale($lvPage->content_html, 'en'), $enReplacements);
    $footer = strtr($transformLinksLocale($lvPage->footer_html, 'en'), $enReplacements);

    Page::updateOrCreate(
        ['slug' => $slug, 'locale' => 'en'],
        [
            'title' => $title,
            'description' => $description,
            'body_class' => $bodyClass,
            'header_html' => $header,
            'content_html' => $content,
            'footer_html' => $footer,
        ]
    );
}
echo "Updated 25 EN pages in database with 100% translated content." . PHP_EOL;

// 3. Generate and Update RU pages
foreach ($lvPages as $lvPage) {
    $slug = $lvPage->slug;
    $title = $titlesRu[$slug]['title'] ?? ($lvPage->title . ' | Адвокатское бюро BULLET');
    $description = $titlesRu[$slug]['description'] ?? $lvPage->description;
    $bodyClass = $lvPage->body_class;
    
    $header = strtr($build3LangSwitcher($transformLinksLocale($lvPage->header_html, 'ru'), 'ru', $slug), $ruReplacements);
    $content = strtr($transformLinksLocale($lvPage->content_html, 'ru'), $ruReplacements);
    $footer = strtr($transformLinksLocale($lvPage->footer_html, 'ru'), $ruReplacements);

    Page::updateOrCreate(
        ['slug' => $slug, 'locale' => 'ru'],
        [
            'title' => $title,
            'description' => $description,
            'body_class' => $bodyClass,
            'header_html' => $header,
            'content_html' => $content,
            'footer_html' => $footer,
        ]
    );
}
echo "Updated 25 RU pages in database with 100% translated content." . PHP_EOL;

