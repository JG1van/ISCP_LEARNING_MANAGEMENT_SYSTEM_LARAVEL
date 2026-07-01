<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> SCI Media Online - Platform Pembelajaran Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

    <style>
        :root {
            --warna0: #ffffff;
            --warna1: #000000;
            --warna2: #753422;
            --warna3: #b05b3b;
            --warna4: #d79771;
            --warna5: #fff4e0;
            --warna6: #ffebc9;
            --warna7: #ffd87a;

            --sepia-tone: #704214;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Times New Roman', serif;
            font-size: 17px;

            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        body {

            color: var(--warna1);
            background: linear-gradient(135deg,
                    var(--warna5) 0%,
                    var(--warna6) 100%);
            background-attachment: fixed;
            overflow-x: hidden;
            position: relative;
        }

        .modal-backdrop {
            z-index: auto !important;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {

            font-weight: 700;
        }

        /* Enhanced Parchment Pattern Background */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            pointer-events: none;
            background-color: var(--warna2);
            /* Stone Wall Texture Pattern */
            overflow: hidden;
            z-index: 1;
        }

        /* Paper texture overlay */
        body::after {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' /%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3C/filter%3E%3Crect width='100' height='100' filter='url(%23noise)' opacity='0.05'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1;
        }

        /* Brick container layer */
        #wall {
            position: fixed;
            top: 0;
            left: 0;
            width: 120vw;
            height: 120vh;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
            z-index: 1;
            /* Dibawah efek body::before & body::after */
            pointer-events: none;
        }

        .brick {
            background-color: #8a4b38;
            box-sizing: border-box;
        }

        .content-wrapper {
            position: relative;
            z-index: 2;
        }

        /* Corner Ornaments */
        .section {
            position: relative;

        }

        .section::before,
        .section::after {
            content: "";
            position: absolute;
            width: 90px;
            height: 90px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cpath d='M0,0 Q25,0 25,25 L25,100 M0,0 Q0,25 25,25 L100,25' stroke='%23FFD8A3' stroke-width='7' fill='none' opacity='0.8'/%3E%3Ccircle cx='25' cy='25' r='5' fill='%23FFD8A3'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;

            opacity: 0.7;
            filter: drop-shadow(0px 0px 3px rgba(255, 215, 180, 0.6));
            pointer-events: none;
            z-index: 3;
        }



        .section::before {
            top: 20px;
            left: 20px;
        }

        .section::after {
            bottom: 20px;
            right: 20px;
            transform: rotate(180deg);
        }

        /* Classical Divider */
        .classical-divider {
            width: 100%;
            height: 3px;
            background: linear-gradient(to right,
                    transparent,
                    var(--warna6),
                    transparent);
            margin: 3rem 0;
            position: relative;
        }

        .classical-divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--warna5);
            padding: 0 1rem;
            color: var(--warna3);
            font-size: 1.5rem;
        }

        /* Navbar Styling - Wood texture effect */
        .navbar {
            background: linear-gradient(180deg,
                    var(--warna2) 0%,
                    var(--warna3) 100%);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4),
                inset 0 1px 0 rgba(255, 244, 224, 0.1);
            padding: 1rem 0;
            border-bottom: 3px solid var(--warna7);
            position: relative;
        }

        .navbar::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='300' height='150'><path d='M0 40 C75 0 225 80 300 40' stroke='rgba(0,0,0,0.12)' stroke-width='3' fill='none'/><path d='M0 90 C75 50 225 130 300 90' stroke='rgba(0,0,0,0.12)' stroke-width='3' fill='none'/><path d='M0 140 C75 100 225 180 300 140' stroke='rgba(0,0,0,0.12)' stroke-width='3' fill='none'/></svg>");
            background-repeat: repeat;
            background-size: 300px 150px;

            pointer-events: none;
        }

        .navbar-brand {
            font-weight: 900;
            color: var(--warna0) !important;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
        }

        .navbar-nav .nav-link {
            color: var(--warna6) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
            position: relative;
            font-size: 12px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .navbar-nav .nav-link:hover {
            color: var(--warna0) !important;
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link::after {
            content: "";
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--warna4);
            transition: width 0.3s ease;
        }

        .navbar-nav .nav-link:hover::after {
            width: 80%;
        }

        .btn-primary-custom {
            background: radial-gradient(circle at 30% 30%, #a83232, #6d1a1a);
            color: #fff;

            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            box-shadow:
                0 4px 0 #4a1212,
                0 5px 10px rgba(0, 0, 0, 0.4);
            position: relative;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
            z-index: 999;

            /* tambahan penting */
            text-decoration: none;
            display: inline-block;
            width: auto;
            min-width: 250px;
            /* opsional, biar seragam */
        }


        .btn-primary-custom:hover {
            transform: translateY(2px);
            box-shadow:
                0 2px 0 #4a1212,
                0 3px 5px rgba(0, 0, 0, 0.4);
            background: radial-gradient(circle at 30% 30%, #b93a3a, #7e1e1e);
            color: #ffebc9;
        }

        .btn-primary-custom:active {
            transform: translateY(4px);
            box-shadow: none;
        }


        .btn-whatsapp {
            background: #25d366;
            border: 2px solid #128c7e;
            color: var(--warna0);
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 2;
        }

        .btn-whatsapp:hover {
            background: #128c7e;
            transform: scale(1.05);
            box-shadow: 0 0 25px rgba(37, 211, 102, 0.7),
                0 6px 12px rgba(0, 0, 0, 0.3);
            color: var(--warna0);
        }

        /* Hero Section */
        .hero-section {
            margin-top: 90px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            background: var(--warna2);

            border-top: 30px ridge var(--warna7);
            border-bottom: 30px groove var(--warna7);
            border-left: 30px ridge var(--warna7);
            border-right: 30px groove var(--warna7);

            /* background: linear-gradient(135deg,
                    var(--warna5) 0%,
                    var(--warna6) 50%,
                    var(--warna5) 100%); */
        }

        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(164, 116, 103, 0.7),
                    rgb(117, 52, 34)),
                url("{{ asset('images/bg-6.webp') }}") center / cover no-repeat;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            color: var(--warna0);
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.15);
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: var(--warna0);
            margin-bottom: 2rem;
            line-height: 1.8;
        }

        .hero-ornament {
            width: 100%;
            max-width: 500px;
            height: auto;
            filter: drop-shadow(4px 4px 8px rgba(0, 0, 0, 0.2));
        }

        /* Decorative Ornament */
        .ornament-divider {
            text-align: center;
            margin: 3rem 0;
            position: relative;
        }

        .ornament-divider svg {
            width: 120px;
            height: auto;
            fill: var(--warna3);
            opacity: 0.7;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.1));
        }

        /* Section Styling */
        .section {
            padding: 5rem 0;
            position: relative;
        }

        .section-title {
            font-size: 2.5rem;
            color: var(--warna2);
            text-align: center;
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .section-title::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 4px;
            background: linear-gradient(to right,
                    transparent,
                    var(--warna4),
                    transparent);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .section-title::before {
            content: "◆";
            position: absolute;
            left: -30px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--warna4);
            font-size: 1rem;
        }

        .section-subtitle {
            text-align: center;
            color: var(--warna3);
            font-size: 1.1rem;
            margin-bottom: 3rem;
            font-style: italic;
        }

        #tentang,
        #fitur-guru,
        #cara-pakai,
        #faq {
            border-top: 30px ridge var(--warna7);
            border-bottom: 30px groove var(--warna7);
            border-left: 30px ridge var(--warna7);
            border-right: 30px groove var(--warna7);
        }

        #tentang::after,
        #fitur-guru::after,
        #cara-pakai::after,
        #faq::after {
            background-image: none;
        }

        #tentang::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(164, 116, 103, 0.7),
                    rgb(117, 52, 34)),
                url("{{ asset('images/bg-2.svg') }}") center / cover no-repeat;
            opacity: 1;
            z-index: -1;
        }

        #cara-pakai::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(164, 116, 103, 0.7),
                    rgb(117, 52, 34)),
                url("{{ asset('images/bg-7.webp') }}") center / cover no-repeat;
            opacity: 1;
            z-index: -1;
        }

        #fitur-guru::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(168, 138, 130, 0.7),
                    rgb(129, 75, 60)),
                url("{{ asset('images/bg-5.webp') }}") center / cover no-repeat;
            opacity: 1;
            z-index: -1;
        }

        #faq::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(164, 116, 103, 0.7),
                    rgb(117, 52, 05)),
                url("{{ asset('images/bg-4.webp') }}") center / cover no-repeat;
            opacity: 1;
            z-index: -1;
        }

        /* Card Styling with enhanced shadows */
        .feature-card {
            background-color: var(--warna0);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 200 200'%3E%3Cdefs%3E%3ClinearGradient id='a' gradientUnits='userSpaceOnUse' x1='88' y1='88' x2='0' y2='0'%3E%3Cstop offset='0' stop-color='%23511502'/%3E%3Cstop offset='1' stop-color='%236c2c1b'/%3E%3C/linearGradient%3E%3ClinearGradient id='b' gradientUnits='userSpaceOnUse' x1='75' y1='76' x2='168' y2='160'%3E%3Cstop offset='0' stop-color='%23868686'/%3E%3Cstop offset='0.09' stop-color='%23ababab'/%3E%3Cstop offset='0.18' stop-color='%23c4c4c4'/%3E%3Cstop offset='0.31' stop-color='%23d7d7d7'/%3E%3Cstop offset='0.44' stop-color='%23e5e5e5'/%3E%3Cstop offset='0.59' stop-color='%23f1f1f1'/%3E%3Cstop offset='0.75' stop-color='%23f9f9f9'/%3E%3Cstop offset='1' stop-color='%23FFFFFF'/%3E%3C/linearGradient%3E%3Cfilter id='c' x='0' y='0' width='200%25' height='200%25'%3E%3CfeGaussianBlur in='SourceGraphic' stdDeviation='12' /%3E%3C/filter%3E%3C/defs%3E%3Cpolygon fill='url(%23a)' points='0 174 0 0 174 0'/%3E%3Cpath fill='%23000' fill-opacity='0.32' filter='url(%23c)' d='M121.8 174C59.2 153.1 0 174 0 174s63.5-73.8 87-94c24.4-20.9 87-80 87-80S107.9 104.4 121.8 174z'/%3E%3Cpath fill='url(%23b)' d='M142.7 142.7C59.2 142.7 0 174 0 174s42-66.3 74.9-99.3S174 0 174 0S142.7 62.6 142.7 142.7z'/%3E%3C/svg%3E");
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-position: top left;
            border: 3px solid var(--warna7);
            border-radius: 12px;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
            position: relative;
        }

        .feature-card::before {
            content: "";
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 1px solid var(--warna7);
            border-radius: 8px;
            opacity: 0.3;
            pointer-events: none;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 16px 32px rgba(117, 52, 34, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);

        }

        .feature-icon {
            font-size: 3rem;
            color: var(--sepia-tone);
            margin-bottom: 1rem;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.2));
            opacity: 0.85;
        }

        .feature-title {
            font-size: 1.3rem;
            color: var(--warna2);
            margin-bottom: 1rem;
        }

        .feature-description {
            color: var(--warna1);
            line-height: 1.6;
        }

        /* Timeline Styling */
        .timeline {
            position: relative;
            padding: 2rem 0;
        }

        .timeline-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            position: relative;
        }

        .timeline-icon {
            width: 60px;
            height: 60px;
            background: var(--warna3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--warna0);
            font-size: 1.5rem;
            flex-shrink: 0;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25),
                inset 0 2px 4px rgba(255, 255, 255, 0.2);
            border: 3px solid var(--warna7);
        }

        .timeline-content {
            margin-left: 2rem;
            background: var(--warna0);
            padding: 1.5rem;
            border-radius: 8px;
            border: 3px solid var(--warna7);
            flex-grow: 1;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .timeline-content h4 {
            color: var(--warna2);
            margin-bottom: 0.5rem;
        }

        /* Accordion Styling */
        .accordion-item {
            background: var(--warna0);
            border: 3px solid var(--warna7);
            margin-bottom: 1rem;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .accordion-button {
            background: var(--warna6);
            color: var(--warna2);
            font-weight: 600;
            font-size: 1.1rem;
            padding: 1.2rem;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        .accordion-button:not(.collapsed) {
            background: var(--warna3);
            color: var(--warna0);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(176, 91, 59, 0.25);
            border-color: var(--warna3);
        }

        .accordion-body {
            padding: 1.5rem;
            background: var(--warna0);
        }

        /* Testimonial Card */
        .testimonial-card {
            background: var(--warna6);
            border: 3px solid var(--warna7);
            border-radius: 12px;
            padding: 2rem;
            position: relative;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: -20px;
            left: 20px;
            font-size: 5rem;
            color: var(--warna3);
            opacity: 0.4;

        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 1rem;
            color: var(--warna1);
        }

        .testimonial-author {
            font-weight: 600;
            color: var(--warna2);
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg,
                    var(--warna2) 0%,
                    var(--warna3) 100%);
            color: var(--warna0);
            padding: 5rem 0;
            text-align: center;
            box-shadow: inset 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .cta-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='300' height='150'><path d='M0 40 C75 0 225 80 300 40' stroke='rgba(0,0,0,0.12)' stroke-width='3' fill='none'/><path d='M0 90 C75 50 225 130 300 90' stroke='rgba(0,0,0,0.12)' stroke-width='3' fill='none'/><path d='M0 140 C75 100 225 180 300 140' stroke='rgba(0,0,0,0.12)' stroke-width='3' fill='none'/></svg>");
            background-repeat: repeat;
            background-size: 300px 150px;


            pointer-events: none;
        }

        .cta-section h2 {
            color: var(--warna0);
            margin-bottom: 2rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .btn-cta-large {
            background: var(--warna0);
            color: var(--warna2);
            padding: 1rem 3rem;
            font-size: 1.2rem;
            border-radius: 50px;
            border: 3px solid var(--warna7);
            font-weight: 700;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
            animation: heartbeat 2s infinite;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        }

        .btn-cta-large:hover {
            transform: scale(1.1);
            box-shadow: 0 0 35px rgba(255, 255, 255, 0.6),
                0 8px 20px rgba(0, 0, 0, 0.4);
            color: var(--warna2);
        }

        /* judul & subjudul */
        .fitur-guru-judul {
            font-weight: bold;
            margin-bottom: 1rem;
            color: var(--warna2);
            /* lebih tegas */
        }

        .fitur-guru-subjudul {
            color: var(--warna3);
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        /* lingkaran fitur */
        .fitur-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            border: 2px solid var(--warna1);
            transition: 0.3s;
        }

        .fitur-circle:hover {
            background: var(--warna4);
            color: var(--warna0);
        }

        .fitur-circle i {
            font-size: 2.2rem;
        }

        /* teks fitur */
        .fitur-text {
            text-align: center;
            margin-top: .7rem;
            font-weight: 600;
            color: var(--warna2);
            font-size: 1rem;
        }

        .klik {
            box-shadow: 0 10px 0 var(--warna1);
            /*Warna bayangan*/
        }

        .klik:focus {
            border: none;
            /*Warna bayangan*/
            outline: none;
            /*Warna bayangan*/
        }

        .klik:active {
            box-shadow: 0 5px var(--warna0);
            /*Warna bayangan*/
            transform: translateY(5px);
            /*Warna bayangan*/
        }



        @keyframes heartbeat {

            0%,
            100% {
                transform: scale(1);
            }

            10%,
            30% {
                transform: scale(1.05);
            }

            20% {
                transform: scale(1);
            }
        }

        /* Footer */
        footer {
            background: linear-gradient(rgba(117, 52, 34, 0.7),
                    rgb(117, 52, 34)),
                url("{{ asset('images/bg-8.webp') }}") center / cover no-repeat;

            color: var(--warna0);
            padding: 3rem 0 1rem;
            box-shadow: inset 0 4px 8px rgba(0, 0, 0, 0.3);
            position: relative;
        }


        footer::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        footer a {
            color: var(--warna0);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        /* Table Styling */
        .table-custom {
            background: var(--warna0);
            border: 3px solid var(--warna4);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .table-custom thead {
            background: var(--warna3);
            color: var(--warna0);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .table-custom tbody tr:hover {
            background: var(--warna6);
        }

        .navbar-fixed {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 2000;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                margin-top: 85px;
            }

            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .timeline-item {
                flex-direction: column;
            }

            .timeline-content {
                margin-left: 0;
                margin-top: 1rem;
            }

            .section::before,
            .section::after {
                width: 50px;
                height: 50px;
            }
        }

        /* Scroll Animation */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <div id="wall"></div>
    <div class="content-wrapper">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark sticky-top navbar-fixed">
            <div class="container">
                <a class="navbar-brand" href="#"> SCI Media Online </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link" href="#fitur">Fitur</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#cara-pakai">Cara Pakai</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#rekap-nilai">Rekap Nilai</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#fitur-guru">Fitur Guru</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#faq">Pertanyaan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#kontak">Kontak</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a href="{{ route('login') }}" class="btn-primary-custom text-center">
                                Login
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7 hero-content" data-aos="fade-right">
                        <h1 class="hero-title">
                            Platform Pembelajaran Digital Terpadu
                        </h1>
                        <p class="hero-subtitle">
                            SCI Media Online menghadirkan solusi Learning
                            Management System (LMS) yang mudah, fleksibel,
                            dan dapat diakses kapan saja, di mana saja untuk
                            guru dan siswa.
                        </p>
                    </div>
                    <div class="col-lg-5 text-center mt-5" data-aos="fade-left">
                        <svg width="500" height="500" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="paperGradient" x1="0%" y1="0%" x2="100%"
                                    y2="0%">
                                    <stop offset="0%" style="stop-color:#e3d0b0;stop-opacity:1" />
                                    <stop offset="15%" style="stop-color:#fdf5e6;stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:#fffaf0;stop-opacity:1" />
                                    <stop offset="85%" style="stop-color:#fdf5e6;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#e3d0b0;stop-opacity:1" />
                                </linearGradient>

                                <linearGradient id="rollerGradient" x1="0%" y1="0%" x2="0%"
                                    y2="100%">
                                    <stop offset="0%" style="stop-color:#8b5a2b;stop-opacity:1" />
                                    <stop offset="30%" style="stop-color:#d2b48c;stop-opacity:1" />
                                    <stop offset="60%" style="stop-color:#8b5a2b;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#5d4037;stop-opacity:1" />
                                </linearGradient>

                                <linearGradient id="quillGradient" x1="0%" y1="0%" x2="100%"
                                    y2="100%">
                                    <stop offset="0%" style="stop-color:#daa520;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#8b4513;stop-opacity:1" />
                                </linearGradient>
                            </defs>

                            <!-- Main Paper Body -->
                            <rect x="100" y="60" width="300" height="380" fill="url(#paperGradient)"
                                stroke="#8b5a2b" stroke-width="1" />

                            <!-- Texture Lines -->
                            <g stroke="#8b5a2b" stroke-width="0.5" opacity="0.3">
                                <line x1="120" y1="100" x2="380" y2="100" />
                                <line x1="130" y1="130" x2="370" y2="130" />
                                <line x1="115" y1="160" x2="385" y2="160" />
                                <line x1="125" y1="190" x2="375" y2="190" />
                                <line x1="125" y1="310" x2="375" y2="310" />
                                <line x1="115" y1="340" x2="385" y2="340" />
                                <line x1="130" y1="370" x2="370" y2="370" />
                                <line x1="120" y1="400" x2="380" y2="400" />
                            </g>

                            <!-- Top Roller -->
                            <g>
                                <rect x="70" y="40" width="360" height="40" rx="5"
                                    fill="url(#rollerGradient)" stroke="#5d4037" stroke-width="1.5" />
                                <circle cx="70" cy="60" r="10" fill="#5d4037" />
                                <circle cx="430" cy="60" r="10" fill="#5d4037" />
                                <rect x="100" y="45" width="300" height="30" fill="#fdf5e6" opacity="0.003" />
                            </g>

                            {{-- <!-- Bottom Roller -->
                            <g>
                                <rect x="70" y="420" width="360" height="40" rx="5"
                                    fill="url(#rollerGradient)" stroke="#5d4037" stroke-width="1.5" />
                                <circle cx="70" cy="440" r="10" fill="#5d4037" />
                                <circle cx="430" cy="440" r="10" fill="#5d4037" />
                                <rect x="100" y="425" width="300" height="30" fill="#fdf5e6" opacity="0.003" />
                            </g> --}}
                            <defs>
                                <!-- ClipPath Oval -->
                                <clipPath id="clipOval">
                                    <ellipse cx="250" cy="250" rx="110" ry="85" />
                                </clipPath>
                            </defs>

                            <!-- Oval luar -->
                            <ellipse cx="250" cy="250" rx="115" ry="90" fill="none"
                                stroke="#8b5a2b" stroke-width="2" opacity="0.5" />

                            <!-- Oval dalam -->
                            <ellipse cx="250" cy="250" rx="108" ry="82" fill="none"
                                stroke="#d2b48c" stroke-width="1" opacity="0.7" />

                            <!-- LOGO dalam oval (crop otomatis) -->
                            <image href="{{ asset('images/logo1.webp') }}" x="140" y="165" width="220"
                                height="170"preserveAspectRatio="xMidYMid meet" clip-path="url(#clipOval)" />

                            <!-- Quill (feather pen) -->
                            <!-- Quill (Feather Pen) + Ink Pot -->
                            <g transform="translate(320, 320) rotate(-15)">

                                <!-- Batang Pena -->
                                <path d="M 60 120 Q 60 60 100 0" stroke="#5a4633" stroke-width="3" fill="none"
                                    stroke-linecap="round" />

                                <!-- Bulu Putih -->
                                <path d="M 60 110 Q 30 80 40 40 Q 50 20 85 5" fill="white" opacity="0.95" />

                                <path d="M 60 110 Q 90 90 110 50 Q 115 30 100 0" fill="white" opacity="0.9" />

                                <!-- Garis serat bulu -->
                                <path d="M 60 100 L 45 80" stroke="#8b8b8b" stroke-width="0.5" />
                                <path d="M 62 90 L 48 70" stroke="#8b8b8b" stroke-width="0.5" />
                                <path d="M 65 80 L 52 60" stroke="#8b8b8b" stroke-width="0.5" />
                                <path d="M 65 100 L 85 85" stroke="#8b8b8b" stroke-width="0.5" />
                                <path d="M 68 90 L 90 75" stroke="#8b8b8b" stroke-width="0.5" />

                                <!-- Wadah Tinta -->
                                <g transform="translate(-40, 60)">
                                    <!-- Leher botol -->
                                    <rect x="50" y="80" width="40" height="12" rx="3" fill="#2b1a1a"
                                        stroke="#1a0f0f" stroke-width="1" />

                                    <!-- Badan botol -->
                                    <rect x="45" y="92" width="50" height="40" rx="8" fill="#3b2727"
                                        stroke="#1a0f0f" stroke-width="1.5" />

                                    <!-- Highlight gelas -->
                                    <ellipse cx="70" cy="105" rx="18" ry="7"
                                        fill="rgba(255,255,255,0.18)" />
                                    <ellipse cx="70" cy="115" rx="15" ry="5"
                                        fill="rgba(255,255,255,0.1)" />
                                </g>

                            </g>


                        </svg>
                        </svg>
                    </div>

                </div>
            </div>
        </section>

        <!-- Ornament Divider -->
        <div class="ornament-divider">
            <svg viewBox="0 0 120 25" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 12 Q 30 2, 60 12 T 120 12" stroke="#B05B3B" stroke-width="2" fill="none" />
                <circle cx="60" cy="12" r="5" fill="#B05B3B" stroke="#753422" stroke-width="1" />
                <circle cx="30" cy="12" r="3" fill="#D79771" stroke="#B05B3B" stroke-width="1" />
                <circle cx="90" cy="12" r="3" fill="#D79771" stroke="#B05B3B" stroke-width="1" />
                <path d="M55,12 L60,7 L65,12 L60,17 Z" fill="#FFF4E0" opacity="0.6" />
            </svg>
        </div>

        <!-- Section: Tentang Aplikasi -->
        <section class="section" id="tentang">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title" data-aos="fade-up" style="color: var(--warna5)">
                        Tentang SCI Media Online
                    </h2>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-8" data-aos="fade-up">
                        <div class="feature-card">
                            <p class="lead text-center mb-4">
                                <strong>SCI Media</strong> merupakan perusahaan penyedia solusi pendidikan
                                digital
                                yang mengembangkan aplikasi <strong>Learning Management System (LMS)</strong>
                                bernama <strong>SCI Media Online</strong> untuk mendukung proses pembelajaran guru dan
                                siswa.
                            </p>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Dapat diakses melalui
                                    <strong>browser</strong> di alamat
                                    <strong> SCI Mediaonline.com</strong>
                                </li>
                                <li class="mb-3">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Dapat diakses dari
                                    <strong>HP atau laptop</strong>
                                </li>
                                <li class="mb-3">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Tersedia aplikasi
                                    <strong>Android</strong> di Google Play
                                    Store
                                </li>
                                <li class="mb-3">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    <strong>Fleksibel</strong> dan mudah
                                    digunakan
                                </li>
                                <li class="mb-3">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Akses
                                    <strong>kapan saja dan di mana saja</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="classical-divider"></div>

        <!-- Section: Fitur Utama -->
        <section class="section" id="fitur">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title" data-aos="fade-up" style="color: var(--warna5)">
                        Fitur Utama
                    </h2>
                    <p class="section-subtitle" data-aos="fade-up" style="color: var(--warna5)">
                        Lengkap, mudah, dan siap mendukung pembelajaran
                        digital Anda
                    </p>
                </div>
                <div class="row row-cols-2 row-cols-lg-4 g-4">
                    <div class="col" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-card text-center">
                            <i class="bi bi-people-fill feature-icon"></i>
                            <h3 class="feature-title">
                                1 Akun Guru + 45 Akun Siswa
                            </h3>
                            <p class="feature-description">
                                Kelola kelas dengan mudah, satu akun guru
                                dapat mengelola hingga 45 siswa secara
                                efektif.
                            </p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-card text-center">
                            <i class="bi bi-camera-video-fill feature-icon"></i>
                            <h3 class="feature-title">
                                Materi Video Bernarasi
                            </h3>
                            <p class="feature-description">
                                Materi pembelajaran dalam bentuk video
                                bernarasi siap pakai, tinggal share ke
                                siswa.
                            </p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-card text-center">
                            <i class="bi bi-file-earmark-text-fill feature-icon"></i>
                            <h3 class="feature-title">Soal Siap Pakai</h3>
                            <p class="feature-description">
                                Bank soal lengkap yang siap digunakan untuk
                                soal dan evaluasi siswa.
                            </p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-card text-center">
                            <i class="bi bi-graph-up-arrow feature-icon"></i>
                            <h3 class="feature-title">
                                Nilai Otomatis per KD
                            </h3>
                            <p class="feature-description">
                                Sistem penilaian otomatis yang terekam per
                                Kompetensi Dasar (KD).
                            </p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-card text-center">
                            <i class="bi bi-calendar-check-fill feature-icon"></i>
                            <h3 class="feature-title">Kegiatan Harian</h3>
                            <p class="feature-description">
                                Pantau dan kelola kegiatan pembelajaran
                                harian dengan mudah.
                            </p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-card text-center">
                            <i class="bi bi-cloud-upload-fill feature-icon"></i>
                            <h3 class="feature-title">
                                Upload Kreativitas Guru
                            </h3>
                            <p class="feature-description">
                                Guru dapat mengunggah materi dan konten
                                kreativitas sendiri.
                            </p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-card text-center">
                            <i class="bi bi-phone-fill feature-icon"></i>
                            <h3 class="feature-title">
                                Akses Kapanpun & Dimanapun
                            </h3>
                            <p class="feature-description">
                                Fleksibilitas akses dari berbagai perangkat,
                                di mana saja dan kapan saja.
                            </p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-card text-center">
                            <i class="bi bi-bookmark-star-fill feature-icon"></i>
                            <h3 class="feature-title">Rekam KI1 & KI2</h3>
                            <p class="feature-description">
                                Pencatatan otomatis untuk Kompetensi Inti 1
                                dan 2 (sikap spiritual dan sosial).
                            </p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-card text-center">
                            <i class="bi bi-chat-dots-fill feature-icon"></i>
                            <h3 class="feature-title">Chat Guru–Siswa</h3>
                            <p class="feature-description">
                                Fitur komunikasi langsung antara guru dan
                                siswa untuk diskusi pembelajaran.
                            </p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="400">
                        <div class="feature-card text-center">
                            <i class="bi bi-broadcast-pin feature-icon"></i>
                            <h3 class="feature-title">Kelas Online Tatap Muka</h3>
                            <p class="feature-description">
                                Kelas virtual interaktif yang memungkinkan guru dan siswa berkomunikasi langsung secara
                                daring.
                            </p>
                        </div>
                    </div>

                    <div class="col" data-aos="fade-up" data-aos-delay="500">
                        <div class="feature-card text-center">
                            <i class="bi bi-headset feature-icon"></i>
                            <h3 class="feature-title">PUSAT LAYANAN PELANGGAN</h3>
                            <p class="feature-description">
                                Sistem ini yang dapat digunakan semua pengguna untuk menyampaikan
                                masalah atau
                                pertanyaan.
                            </p>
                        </div>
                    </div>

                    <div class="col" data-aos="fade-up" data-aos-delay="600">
                        <div class="feature-card text-center">
                            <i class="bi bi-shield-lock-fill feature-icon"></i>
                            <h3 class="feature-title">Akun Recovery</h3>
                            <p class="feature-description">
                                Fitur yang membantu mengembalikan akses akun ketika lupa password atau terjadi kendala
                                login.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <div class="classical-divider"></div>

        <!-- Section: How It Works -->
        <section class="section" id="cara-pakai">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title" data-aos="fade-up" style="color: var(--warna0)">
                        Cara Menggunakan
                    </h2>
                    <p class="section-subtitle" data-aos="fade-up" style="color: var(--warna0)">
                        Mudah diakses dan mudah didapatkan
                    </p>
                </div>

                <div class="row mb-5">
                    <div class="col-lg-6" data-aos="fade-right">
                        <h3 class="mb-4" style="color: var(--warna0)">
                            Cara Akses Aplikasi
                        </h3>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="bi bi-globe"></i>
                                </div>
                                <div class="timeline-content">
                                    <h4>Melalui Browser</h4>
                                    <p>
                                        Akses langsung melalui website
                                        <strong> SCI Mediaonline.com</strong>
                                        dari browser favorit Anda.
                                    </p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="bi bi-phone"></i>
                                </div>
                                <div class="timeline-content">
                                    <h4>Aplikasi Android</h4>
                                    <p>
                                        Download aplikasi
                                        <strong>" SCI Media Online"</strong>
                                        di Google Play Store untuk akses
                                        lebih praktis.
                                    </p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="bi bi-wifi"></i>
                                </div>
                                <div class="timeline-content">
                                    <h4>Koneksi Internet</h4>
                                    <p>
                                        Pastikan perangkat Anda terhubung
                                        dengan koneksi internet yang stabil.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6" data-aos="fade-left">
                        <h3 class="mb-4" style="color: var(--warna0)">
                            Cara Mendapatkan Akun
                        </h3>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="bi bi-cart-check"></i>
                                </div>
                                <div class="timeline-content">
                                    <h4>1. Membeli Produk SCI Media</h4>
                                    <p>
                                        Lakukan pembelian produk SCI Media
                                        melalui distributor resmi atau
                                        hubungi admin kami.
                                    </p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="bi bi-camera"></i>
                                </div>
                                <div class="timeline-content">
                                    <h4>2. Kirim Foto Lisensi</h4>
                                    <p>
                                        Kirimkan foto lisensi produk yang
                                        telah Anda beli ke admin melalui
                                        WhatsApp.
                                    </p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="bi bi-key"></i>
                                </div>
                                <div class="timeline-content">
                                    <h4>3. Terima Username & Password</h4>
                                    <p>
                                        Admin akan mengirimkan username dan
                                        password akun Anda untuk mulai
                                        menggunakan aplikasi.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="classical-divider"></div>

        <!-- Section: Fitur Lengkap Guru -->

        <section class="section" id="rekap-nilai">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title" data-aos="fade-up" style="color: var(--warna5)">
                        Rekap Nilai Otomatis
                    </h2>
                    <p class="section-subtitle" data-aos="fade-up" style="color: var(--warna5)">
                        Pelaporan nilai lengkap, rapi, dan siap unduh hanya
                        dengan satu klik
                    </p>
                </div>

                <div class="row row-cols-2 row-cols-lg-4 g-4">
                    <!-- Card 1 -->
                    <div class="col" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-card text-center p-4">
                            <i class="bi bi-bar-chart-steps"
                                style="
                                        font-size: 3.5rem;
                                        color: var(--warna2);
                                    "></i>
                            <h4 class="mt-3" style="color: var(--warna2)">
                                Rekap UH / PTS / PAS
                            </h4>
                            <p>
                                Semua nilai ulangan otomatis direkap per
                                kompetensi dan per mata pelajaran tanpa
                                perlu menghitung manual.
                            </p>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="col" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-card text-center p-4">
                            <i class="bi bi-list-check"
                                style="
                                        font-size: 3.5rem;
                                        color: var(--warna2);
                                    "></i>
                            <h4 class="mt-3" style="color: var(--warna2)">
                                Rekap Tugas Siswa
                            </h4>
                            <p>
                                Sistem otomatis menggabungkan nilai tugas
                                dari seluruh kegiatan dan menampilkannya
                                dalam grafik yang mudah dipahami.
                            </p>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="col" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-card text-center p-4">
                            <i class="bi bi-clipboard-data"
                                style="
                                        font-size: 3.5rem;
                                        color: var(--warna2);
                                    "></i>
                            <h4 class="mt-3" style="color: var(--warna2)">
                                Laporan Per Mapel
                            </h4>
                            <p>
                                Guru dapat melihat performa siswa pada
                                masing-masing mata pelajaran secara detail
                                dan terstruktur.
                            </p>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="col" data-aos="fade-up" data-aos-delay="400">
                        <div class="feature-card text-center p-4">
                            <i class="bi bi-file-earmark-arrow-down"
                                style="
                                        font-size: 3.5rem;
                                        color: var(--warna2);
                                    "></i>
                            <h4 class="mt-3" style="color: var(--warna2)">
                                Download Excel & PDF
                            </h4>
                            <p>
                                Semua rekap nilai dapat langsung diunduh
                                dalam format Excel atau PDF untuk laporan
                                sekolah atau arsip pribadi.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- <div class="text-center mt-5">
                    <a href="#" class="btn-primary-custom px-4 py-2">
                        <i class="bi bi-file-earmark-bar-graph me-1"></i>
                        Lihat Contoh Laporan
                    </a>
                </div> --}}
            </div>

        </section>



        <div class="classical-divider"></div>

        <!-- Section: Rekap Nilai -->
        <section class="section" id="fitur-guru">
            <div class="container">
                <div class="feature-card m-auto"
                    style="background-image:none;  transition: none;  transform: none; width: 70%;">
                    <div class="container text-center">

                        <!-- Judul -->
                        <h2 class="fitur-guru-judul" data-aos="fade-up" data-aos-delay="50">
                            Fitur Lengkap untuk Guru
                        </h2>
                        <p class="fitur-guru-subjudul" data-aos="fade-up" data-aos-delay="100">
                            Kelola pembelajaran dengan lebih mudah, cepat, dan efisien
                        </p>

                        <!-- Grid Fitur 3x3 -->
                        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-3 g-4 justify-content-center">

                            <!-- Manajemen Profil -->
                            <div class="col" data-aos="fade-up" data-aos-delay="100">
                                <div class="fitur-circle klik" data-bs-toggle="popover" data-bs-placement="top"
                                    data-bs-custom-class="custom-popover-fitur" data-bs-title="Manajemen Profil"
                                    data-bs-content="Edit informasi pribadi, unggah foto profil, perbarui data, dan ganti password akun dengan mudah untuk menjaga keamanan serta kenyamanan penggunaan.">
                                    <i class="bi bi-person-circle"></i>
                                </div>
                                <p class="fitur-text">Manajemen Profil</p>
                            </div>

                            <!-- Kelola Kelas & Siswa -->
                            <div class="col" data-aos="fade-up" data-aos-delay="150">
                                <div class="fitur-circle klik" data-bs-toggle="popover" data-bs-placement="top"
                                    data-bs-custom-class="custom-popover-fitur" data-bs-title="Kelola Kelas & Siswa"
                                    data-bs-content="Atur nama kelas, tambah siswa baru, edit data siswa, hapus siswa, serta pantau laporan KI1 dan KI2 untuk memonitor perkembangan sikap spiritual dan sosial siswa.">
                                    <i class="bi bi-people"></i>
                                </div>
                                <p class="fitur-text">Kelas & Siswa</p>
                            </div>

                            <!-- Tugas & Penilaian -->
                            <div class="col" data-aos="fade-up" data-aos-delay="200">
                                <div class="fitur-circle klik" data-bs-toggle="popover" data-bs-placement="top"
                                    data-bs-custom-class="custom-popover-fitur" data-bs-title="Tugas & Penilaian"
                                    data-bs-content="Lihat daftar tugas siswa, cek detail pengerjaan, berikan nilai serta feedback yang membantu, dan hapus tugas yang sudah tidak diperlukan.">
                                    <i class="bi bi-clipboard-check"></i>
                                </div>
                                <p class="fitur-text">Tugas & Penilaian</p>
                            </div>

                            <!-- Nilai Ulangan -->
                            <div class="col" data-aos="fade-up" data-aos-delay="250">
                                <div class="fitur-circle klik" data-bs-toggle="popover" data-bs-placement="top"
                                    data-bs-custom-class="custom-popover-fitur" data-bs-title="Nilai Ulangan"
                                    data-bs-content="Akses nilai ulangan siswa secara lengkap, lihat jawaban setiap soal dengan jelas, dan lakukan reset nilai jika diperlukan revisi atau perbaikan.">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <p class="fitur-text">Nilai Ulangan</p>
                            </div>

                            <!-- Materi Pembelajaran -->
                            <div class="col" data-aos="fade-up" data-aos-delay="300">
                                <div class="fitur-circle klik" data-bs-toggle="popover" data-bs-placement="top"
                                    data-bs-custom-class="custom-popover-fitur" data-bs-title="Materi Pembelajaran"
                                    data-bs-content="Buat materi sendiri, upload file PDF/Word/Video, tambahkan link sumber belajar, dan bagikan materi bawaan dari  SCI Media untuk pembelajaran yang lebih lengkap.">
                                    <i class="bi bi-book"></i>
                                </div>
                                <p class="fitur-text">Materi</p>
                            </div>

                            <!-- Memberi Tugas -->
                            <div class="col" data-aos="fade-up" data-aos-delay="350">
                                <div class="fitur-circle klik" data-bs-toggle="popover" data-bs-placement="top"
                                    data-bs-custom-class="custom-popover-fitur" data-bs-title="Memberikan Tugas"
                                    data-bs-content="Buat tugas baru, tambahkan penjelasan lengkap, sertakan file pendukung, dan kirim tugas langsung kepada siswa yang dituju secara otomatis.">
                                    <i class="bi bi-pencil-square"></i>
                                </div>
                                <p class="fitur-text">Memberi Tugas</p>
                            </div>

                            <!-- Soal Soal -->
                            <div class="col" data-aos="fade-up" data-aos-delay="400">
                                <div class="fitur-circle klik" data-bs-toggle="popover" data-bs-placement="top"
                                    data-bs-custom-class="custom-popover-fitur" data-bs-title="Soal Soal"
                                    data-bs-content="Gunakan soal utama dari bank soal  SCI Media, buat soal baru sendiri, edit atau hapus soal, cetak soal untuk ujian offline, dan bagikan untuk dikerjakan siswa.">
                                    <i class="bi bi-question-circle"></i>
                                </div>
                                <p class="fitur-text">Soal Soal</p>
                            </div>

                            <!-- Rekap Nilai -->
                            <div class="col" data-aos="fade-up" data-aos-delay="450">
                                <div class="fitur-circle klik" data-bs-toggle="popover" data-bs-placement="top"
                                    data-bs-custom-class="custom-popover-fitur" data-bs-title="Rekap Nilai"
                                    data-bs-content="Lihat rekap UH, PTS, PAS, nilai tugas, per mata pelajaran, dan unduh laporan lengkap dalam bentuk Excel atau PDF secara instan.">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                                <p class="fitur-text">Rekap Nilai</p>
                            </div>

                            <!-- Kelas Online -->
                            <div class="col" data-aos="fade-up" data-aos-delay="500">
                                <div class="fitur-circle klik" data-bs-toggle="popover" data-bs-placement="top"
                                    data-bs-custom-class="custom-popover-fitur"
                                    data-bs-title="Kelas Online Tatap Muka"
                                    data-bs-content="Selenggarakan kelas virtual interaktif seperti Zoom/Google Meet, dukungan video conference, tanpa instalasi rumit siswa cukup klik dan langsung masuk.">
                                    <i class="bi bi-broadcast-pin"></i>
                                </div>
                                <p class="fitur-text">Kelas Online</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="classical-divider"></div>

        <!-- Section: Testimoni -->
        <section class="section">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title" data-aos="fade-up" style="color: var(--warna5)">
                        Testimoni Pengguna
                    </h2>
                    <p class="section-subtitle" data-aos="fade-up" style="color: var(--warna5)">
                        Apa kata mereka tentang SCI Media Online
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="testimonial-card">
                            <p class="testimonial-text">
                                " SCI Media Online sangat membantu saya dalam
                                mengelola pembelajaran jarak jauh.
                                Fitur-fiturnya lengkap dan mudah digunakan!"
                            </p>
                            <p class="testimonial-author">
                                — Ibu Sari, Guru SD
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="testimonial-card">
                            <p class="testimonial-text">
                                "Dengan SCI Media Online, saya bisa memantau
                                perkembangan siswa secara real-time. Rekap
                                nilai otomatis sangat menghemat waktu."
                            </p>
                            <p class="testimonial-author">
                                — Pak Budi, Guru SMP
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="testimonial-card">
                            <p class="testimonial-text">
                                "Materi video bernarasi dan bank soal yang
                                tersedia membuat persiapan mengajar jadi
                                lebih efisien dan mudah. Sangat direkomendasikan!"
                            </p>
                            <p class="testimonial-author">
                                — Ibu Dewi, Guru SMA
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="classical-divider"></div>

        <!-- Section: Bantuan  -->
        <section class="section" id="faq">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title" data-aos="fade-up" style="color: var(--warna0)">
                        Butuh Bantuan atau Ada Pertanyaan?
                    </h2>
                    <p class="section-subtitle" data-aos="fade-up" style="color: var(--warna0)">
                        Jika Anda mengalami kendala atau ingin mengajukan
                        pertanyaan, silakan gunakan fitur pusat layanan pelanggan SCI Media yang
                        telah kami sediakan.
                    </p>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8" data-aos="fade-up">
                        <div class="p-4 text-center feature-card">
                            <i class="bi bi-life-preserver"
                                style="
                        font-size: 4rem;
                        color: var(--warna2);
                    ">
                            </i>

                            <h3 class="mt-3" style="color: var(--warna2)">
                                PUSAT LAYANAN PELANGGAN<br>
                                SCI Media Online
                            </h3>

                            <p class="mt-2">
                                Untuk segala kebutuhan bantuan, pertanyaan, atau laporan masalah,
                                Anda dapat menggunakan layanan yang telah kami sediakan.
                                Silakan klik tombol di bawah ini untuk masuk ke halaman
                                <strong>Pusat Layanan Pelanggan SCI Media Online</strong>, lalu pilih menu layanan
                                yang sesuai
                                untuk memulai layanan baru atau melanjutkan layanan Anda sebelumnya.
                            </p>

                            <a href="{{ route('layanan-pelanggan.index') }}"
                                class="btn-primary-custom mt-3 px-4 py-2">
                                <i class="bi bi-chat-dots-fill me-1"></i>
                                Pergi
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div data-aos="zoom-in">
                    <h2 class="mb-4">Siap Memulai Pembelajaran Digital?</h2>
                    <p class="lead mb-5">
                        Ayo mulai sekarang! Pilih salah satu opsi di bawah
                        untuk melakukan pemesanan atau menghubungi admin
                        melalui metode yang Anda inginkan.
                    </p>

                    <div class="d-flex gap-3 justify-content-center flex-wrap">

                        <!-- Tombol WhatsApp -->
                        <a href="https://wa.me/6282327042255" target="_blank" class="btn-primary-custom"
                            style="background: #25d366; color: white; border-color: #128c7e;">
                            <i class="bi bi-whatsapp"></i> Hubungi lewat WhatsApp
                        </a>

                        <!-- Tombol Toko Online -->
                        <a href="#" data-bs-toggle="modal" data-bs-target="#popupTokoOnline"
                            class="btn-primary-custom">
                            <i class="bi bi-cart-check"></i> Toko Online
                        </a>


                        <!-- Tombol Layanan Pelanggan -->
                        <a href="{{ route('layanan-pelanggan.index') }}" class="btn-primary-custom"
                            style="background: var(--warna3); color: var(--warna0); border-color: var(--warna2);">
                            <i class="bi bi-chat-dots-fill"></i> Hubungi Layanan Pelanggan
                        </a>

                    </div>
                </div>
            </div>
        </section>
        <!-- MODAL TOKO ONLINE -->
        <div class="modal fade" id="popupTokoOnline" tabindex="-1" aria-hidden="true"
            style="z-index: 999999 !important; position: fixed !important;">
            <div class="modal-dialog modal-dialog-centered"
                style="z-index: 1000000 !important; position: relative !important;">
                <div class="modal-content" style="z-index: 1000001 !important;">

                    <div class="modal-header" style="background: var(--warna2); color: var(--warna0);">
                        <h5 class="modal-title text-center w-100">Toko Online Resmi SCI Media</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label fw-bold text-center w-100 mb-3">
                            Pilih platform resmi untuk pembelian produk SCI Media:
                        </label>

                        <div class="d-grid gap-2">

                            <a href="https:// SCI Media.co.id/" target="_blank"
                                class="btn-primary-custom w-100 text-center"
                                style="background: var(--warna1); color: var(--warna0); border: 2px solid var(--warna2);">
                                SCI Media Website
                            </a>

                            <a href="https://www.tokopedia.com/sci-media" target="_blank"
                                class="btn-primary-custom w-100 text-center"
                                style="background: var(--warna2); color: var(--warna0); border: 2px solid var(--warna3);">
                                Tokopedia
                            </a>

                            <a href="https://id.shp.ee/Lgf5Frg" target="_blank"
                                class="btn-primary-custom w-100 text-center"
                                style="background: var(--warna3); color: var(--warna0); border: 2px solid var(--warna1);">
                                Shopee
                            </a>

                            <a href="https://siplahtelkom.com/product/alat-peraga-sekolah/1932102-media-ajar-sci-rpp-dan-adminitrasi-guru-"
                                target="_blank" class="btn-primary-custom w-100 text-center"
                                style="background: var(--warna4); color: var(--warna0); border: 2px solid var(--warna2);">
                                Siplah Telkom
                            </a>

                            <a href="https://siplah.blibli.com/merchant-detail/SSME-0028?itemPerPage=40&page=0&merchantId=SSME-0028"
                                target="_blank" class="btn-primary-custom w-100 text-center"
                                style="background: var(--warna5); color: var(--warna1); border: 2px solid var(--warna3);">
                                Siplah Blibli
                            </a>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn-primary-custom w-100"
                            style="background: var(--warna0); color: var(--warna3); border: 2px solid var(--warna2);"
                            data-bs-dismiss="modal">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>




        <!-- Footer -->
        <footer id="kontak">
            <div class="container">
                <div class="row">
                    <!-- Bagian Informasi -->
                    <div class="col-8 mb-4">
                        <h5 class="mb-3"> SCI Media Online</h5>
                        <p>
                            SCI Media Online adalah platform Learning Management System (LMS)
                            terpercaya yang dirancang untuk mendukung proses pembelajaran
                            digital secara lebih efektif, fleksibel, dan mudah digunakan.
                        </p>
                        <p>
                            Dengan berbagai fitur seperti pengelolaan kelas, penilaian,
                            manajemen materi, hingga laporan hasil belajar,
                            kami berkomitmen membantu guru, siswa, dan institusi pendidikan
                            mencapai pengalaman belajar yang lebih modern dan berkualitas.
                        </p>
                        <p>
                            Kami terus mengembangkan layanan agar dapat memberikan pengalaman
                            terbaik, baik untuk kebutuhan pembelajaran sehari-hari maupun
                            pengelolaan sistem pendidikan secara keseluruhan.
                        </p>
                    </div>

                    <!-- Bagian Kontak -->
                    <div class="col-4 mb-4">
                        <h5 class="mb-3">Kontak</h5>

                        <p>
                            <i class="bi bi-whatsapp me-2"></i>
                            <a href="https://wa.me/6282327042255" target="_blank">
                                0823-2704-2255
                            </a>
                        </p>

                        <p>
                            <i class="bi bi-instagram me-2"></i>
                            <a href="https://instagram.com/ SCI Mediaonline" target="_blank">
                                @ SCI Mediaonline
                            </a>
                        </p>
                        <p>
                            <i class="bi bi-globe me-2"></i>
                            <a href="https:// SCI Mediaonline.com" target="_blank">
                                SCI Mediaonline.com
                            </a>
                        </p>

                        <p>
                            <i class="bi bi-geo-alt-fill me-2"></i>
                            Indonesia
                        </p>
                    </div>
                </div>

                <hr style="border-color: var(--warna4)" />

                <div class="text-center py-3">
                    <p class="mb-0">
                        &copy; 2025 SCI Media Online. Semua hak cipta dilindungi.
                    </p>
                    <small class="d-block mt-1" style="opacity: 0.7;">
                        Dikembangkan untuk mendukung transformasi pendidikan digital.
                    </small>
                </div>
            </div>
        </footer>

    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AOS Animation JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
        });

        // Form submission handler
        document
            .getElementById("ctaForm")
            .addEventListener("submit", function(e) {
                e.preventDefault();

                // Close modal
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("ctaModal")
                );
                modal.hide();

                // Show success toast
                const toast = new bootstrap.Toast(
                    document.getElementById("successToast")
                );
                toast.show();

                // Reset form
                this.reset();
            });

        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
            anchor.addEventListener("click", function(e) {
                const href = this.getAttribute("href");
                if (href !== "#" && href !== "#ctaModal") {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: "smooth",
                            block: "start",
                        });
                    }
                }
            });
        });

        // Navbar background on scroll
        window.addEventListener("scroll", function() {
            const navbar = document.querySelector(".navbar");
            if (window.scrollY > 50) {
                navbar.style.boxShadow = "0 6px 24px rgba(0, 0, 0, 0.5)";
            } else {
                navbar.style.boxShadow = "0 6px 20px rgba(0, 0, 0, 0.4)";
            }
        });
    </script>
    <script>
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        const popovers = [];

        // Inisialisasi semua popover
        popoverTriggerList.forEach((el) => {
            const pop = new bootstrap.Popover(el);
            popovers.push(pop);

            // Saat tombol diklik, tutup semua popover lain
            el.addEventListener("click", function(e) {
                // Tutup popover lainnya
                popovers.forEach((p) => {
                    if (p !== pop) p.hide();
                });
            });

            // Auto hide setelah 5 detik
            el.addEventListener("shown.bs.popover", () => {
                setTimeout(() => {
                    pop.hide();
                }, 5000);
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const wall = document.getElementById("wall");

            const rows = 15;
            const bricksPerRow = 12;

            const colors = [
                "#8a4b38",
                "#953a2d",
                "#9e5e4b",
                "#933125",
                "#7d4436",
            ];

            const totalBricks = Math.floor(rows * bricksPerRow * 1.5);

            for (let i = 0; i < totalBricks; i++) {
                const brick = document.createElement("div");
                brick.classList.add("brick");

                // Ukuran acak
                const width = Math.floor(Math.random() * 60) + 80;
                const height = Math.floor(Math.random() * 10) + 55;
                brick.style.width = `${width}px`;
                brick.style.height = `${height}px`;

                // Warna acak
                brick.style.backgroundColor =
                    colors[Math.floor(Math.random() * colors.length)];

                // Border-radius acak
                const b1 = Math.floor(Math.random() * 10) + 2;
                const b2 = Math.floor(Math.random() * 10) + 2;
                const b3 = Math.floor(Math.random() * 10) + 2;
                const b4 = Math.floor(Math.random() * 10) + 2;
                brick.style.borderRadius = `${b1}px ${b2}px ${b3}px ${b4}px`;

                // Rotasi acak
                const rotation = (Math.random() * 2 - 1).toFixed(1);
                brick.style.transform = `rotate(${rotation}deg)`;

                // Margin acak
                const marginX = Math.floor(Math.random() * 4) + 1;
                const marginY = Math.floor(Math.random() * 4) + 1;
                brick.style.margin = `${marginY}px ${marginX}px`;

                wall.appendChild(brick);
            }
        });
    </script>

</body>

</html>
