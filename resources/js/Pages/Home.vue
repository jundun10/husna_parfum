<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const logoUrl = '/images/logo-hf.png';
const heroUrl = '/images/hero.jpeg';
const aboutParfumUrl = '/images/parfume 3.jpg';

const productImages = [
    '/images/parfum 1.jpg',
    '/images/parfum 2.jpg',
    '/images/parfum 3.jpg',
    '/images/parfum 4.jpg',
];

const logout = () => {
    const form = useForm({});
    form.post('/logout');
};
</script>

<template>
    <Head title="Home" />

    <div class="home-page">

        <nav class="navbar">

            <Link
                href="/"
                class="logo"
            >
                <img
                    :src="logoUrl"
                    alt="HF Parfum"
                >
            </Link>

            <div class="nav-menu">
                <a href="#beranda">Beranda</a>
                <a href="#tentang">Tentang Kami</a>
                <a href="#keunggulan">Keunggulan</a>
                <a href="#produk">Produk</a>
                <a href="#kontak">Kontak</a>
            </div>

            <a
    href="/login"
    class="logout-button"
>
    Login / Daftar
</a>

        </nav>


        
        <main id="beranda">

            
<section class="hero" id="beranda">

    <img
        :src="heroUrl"
        alt="HF parfum"
        class="hero-image"
    >

    <div class="hero-overlay"></div>

    <div class="hero-content">

        <span class="hero-label">
            WELCOME TO
        </span>

        <h1>
            HF
            <span>Parfum</span>
        </h1>

        <p>
            Temukan aroma yang menggambarkan
            karakter dan kepribadianmu.
        </p>

        <a
            href="/Pelanggan"
            class="hero-button"
        >
            Pesan Sekarang
        </a>

    </div>

</section>

           
            <section
    id="tentang"
    class="about-section"
>

    <div class="section-container">

        <div class="about-content">

            <span class="section-label">
                TENTANG KAMI
            </span>

            <h2>
                Aroma yang
                <span>menjadi cerita.</span>
            </h2>

            <p>
                HF Perfum hadir untuk memberikan
                pengalaman parfum yang elegan, lembut,
                dan berkesan dalam setiap kesempatan.
            </p>

            <p>
                Kami percaya bahwa parfum bukan hanya
                sekadar wewangian, tetapi bagian dari
                karakter dan identitas seseorang.
            </p>

            <a
                href="#keunggulan"
                class="text-button"
            >
                Kenali Kami
                <span>→</span>
            </a>

        </div>

    </div>

</section>

            <section
                id="keunggulan"
                class="features-section"
            >

                <div class="section-heading">

                    <span class="section-label">
                        KEUNGGULAN KAMI
                    </span>

                    <h2>
                        Mengapa memilih
                        <span>HF Parfum?</span>
                    </h2>

                    <p>
                        Kami menghadirkan pengalaman parfum
                        yang sederhana, elegan, dan berkesan.
                    </p>

                </div>


                <div class="features-grid">

                    <div class="feature-card">

                        <div class="feature-number">
                            01
                        </div>

                        <div class="feature-icon">
                            ✦
                        </div>

                        <h3>
                            Aroma Elegan
                        </h3>

                        <p>
                            Pilihan aroma yang lembut dan elegan
                            untuk menemani berbagai suasana.
                        </p>

                    </div>


                    <div class="feature-card">

                        <div class="feature-number">
                            02
                        </div>

                        <div class="feature-icon">
                            ♡
                        </div>

                        <h3>
                            Tahan Lama
                        </h3>

                        <p>
                            Dirancang untuk memberikan aroma
                            yang nyaman dan bertahan lebih lama.
                        </p>

                    </div>


                    <div class="feature-card">

                        <div class="feature-number">
                            03
                        </div>

                        <div class="feature-icon">
                            ✧
                        </div>

                        <h3>
                            Untuk Setiap Momen
                        </h3>

                        <p>
                            Temukan aroma yang sesuai untuk aktivitas
                            sehari-hari maupun momen spesial.
                        </p>

                    </div>

                </div>

            </section>

            <section
                id="produk"
                class="products-section"
            >

                <div class="section-heading">

                    <span class="section-label">
                        KOLEKSI KAMI
                    </span>

                    <h2>
                        Temukan aroma
                        <span>favoritmu.</span>
                    </h2>

                    <p>
                        Jelajahi pilihan HF parfum yang
                        dirancang untuk melengkapi karaktermu.
                    </p>

                </div>


                <div class="products-grid">

                <div class="product-card">
                    <div class="product-image">
                        <img
                            :src="productImages[0]"
                            alt="Parfum Lamore 1"
                        >
                    </div>

                    <div class="product-info">
                        <span class="product-category">
                            COLLECTION 01
                        </span>

                        <h3>
                            Miss Dior
                        </h3>

                        <p>
                            Aroma feminin yang lembut dan elegan.
                        </p>
                    </div>
                </div>


                <div class="product-card">
                    <div class="product-image">
                        <img
                            :src="productImages[1]"
                            alt="Parfum Lamore 2"
                        >
                    </div>

                    <div class="product-info">
                        <span class="product-category">
                            COLLECTION 02
                        </span>

                        <h3>
                            Bleu de Chanel
                        </h3>

                        <p>
                            Karakter segar, maskulin, dan berkelas.
                        </p>
                    </div>
                </div>


                <div class="product-card">
                    <div class="product-image">
                        <img
                            :src="productImages[2]"
                            alt="Parfum Lamore 3"
                        >
                    </div>

                    <div class="product-info">
                        <span class="product-category">
                            COLLECTION 03
                        </span>

                        <h3>
                            Alba Signature
                        </h3>

                        <p>
                            Aroma hangat dengan karakter yang mewah.
                        </p>
                    </div>
                </div>


                <div class="product-card">
                    <div class="product-image">
                        <img
                            :src="productImages[3]"
                            alt="Parfum Lamore 4"
                        >
                    </div>

                    <div class="product-info">
                        <span class="product-category">
                            COLLECTION 04
                        </span>

                        <h3>
                            Sauvage
                        </h3>

                        <p>
                            Aroma modern dengan kesan kuat dan segar.
                        </p>
                    </div>
                </div>

            </div>

            </section>

            <section
                id="kontak"
                class="contact-section"
            >

                <div class="contact-container">

                    <div class="contact-heading">

                        <span class="section-label">
                            HUBUNGI KAMI
                        </span>

                        <h2>
                            Mari terhubung
                            <span>dengan kami.</span>
                        </h2>

                        <p>
                            Punya pertanyaan atau ingin mengetahui
                            lebih banyak tentang koleksi kami?
                            Jangan ragu untuk menghubungi HF Parfum.
                        </p>

                    </div>


                    <div class="contact-grid">

                        <div class="contact-item">

                            <span class="contact-label">
                                EMAIL
                            </span>

                            <h3>
                                hello@lamoreperfumes.com
                            </h3>

                        </div>


                        <div class="contact-item">

                            <span class="contact-label">
                                TELEPON
                            </span>

                            <h3>
                                +62 8XX XXXX XXXX
                            </h3>

                        </div>


                        <div class="contact-item">

                            <span class="contact-label">
                                LOKASI
                            </span>

                            <h3>
                                HF Parfum
                            </h3>

                            <a
                                href="https://maps.app.goo.gl/L2V2VHMM2EZaoDpv7?g_st=awb"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="location-link"
                            >
                                Lihat Lokasi Toko →
                            </a>

                        </div>

                    </div>

                </div>

            </section>


        </main>

        <footer class="footer">

            <div class="footer-logo">
                <img
                    :src="logoUrl"
                    alt="HF Parfum"
                >
            </div>

            <p>
                 © 2026 HF Parfum.
                All rights reserved.
            </p>

        </footer>

    </div>
</template>


<style scoped>

* {
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, Helvetica, sans-serif;
    color: #5c665f;
}

.location-link {
    display: inline-block;
    margin-top: 10px;
    color: #4a6b52;
    font-size: 11px;
    letter-spacing: .3px;
    text-decoration: none;
    border-bottom: 1px solid transparent;
    transition: border-color .25s ease, color .25s ease;
}

.location-link:hover {
    color: #38513f;
    border-bottom-color: currentColor;
}

.home-page {
    background: #fdfdfb;
}

.navbar {
    position: sticky;
    top: 0;
    z-index: 100;

    height: 72px;

    display: flex;
    align-items: center;

    padding: 0 40px;

    background: rgba(253, 253, 251, .92);

    border-bottom: 1px solid #e6ebe4;

    backdrop-filter: blur(10px);
}

.logo {
    display: flex;
    align-items: center;

    width: 56px;
    height: 72px;

    text-decoration: none;
}

.logo img {
    width: 50px;
    height: 50px;

    object-fit: contain;

    mix-blend-mode: multiply;
}

.nav-menu {
    flex: 1;

    display: flex;
    justify-content: center;

    gap: 46px;
}

.nav-menu a {
    position: relative;

    padding-bottom: 4px;

    color: #6b746c;

    font-size: 13px;
    letter-spacing: .2px;

    text-decoration: none;

    transition: color .2s ease;
}

.nav-menu a::after {
    content: '';

    position: absolute;
    left: 0;
    right: 100%;
    bottom: 0;

    height: 1px;

    background: #4a6b52;

    transition: right .25s ease;
}

.nav-menu a:hover {
    color: #384c3d;
}

.nav-menu a:hover::after {
    right: 0;
}

.logout-button {
    display: inline-flex;
    align-items: center;

    padding: 9px 20px;

    border: 1px solid #c9d8c8;
    border-radius: 3px;

    background: transparent;

    color: #435a48;
    font-size: 12px;
    letter-spacing: .2px;

    text-decoration: none;

    transition: background .2s ease, border-color .2s ease, color .2s ease;
}

.logout-button:hover {
    background: #435a48;
    border-color: #435a48;
    color: #fdfdfb;
}

.hero {
    position: relative;

    min-height: 620px;

    overflow: hidden;

    display: flex;
    align-items: center;

    background: #e9f0e6;
}

.hero-image {
    position: absolute;

    inset: 0;

    width: 100%;
    height: 100%;

    object-fit: cover;
    object-position: center;
}

.hero-overlay {
    position: absolute;
    inset: 0;

    background: linear-gradient(
        100deg,
        rgba(253, 253, 251, .88) 0%,
        rgba(253, 253, 251, .55) 38%,
        rgba(253, 253, 251, 0) 62%
    );
}

.hero-content {
    position: relative;

    z-index: 2;

    width: 46%;

    padding-left: 9%;

    color: #4d564f;
}

.hero-label {
    display: block;

    margin-bottom: 18px;

    color: #5f8168;
    font-size: 12px;
    letter-spacing: 3px;
}

.hero-content h1 {
    margin: 0;

    font-family: Georgia, 'Times New Roman', serif;
    font-size: 62px;
    font-weight: 400;
    line-height: 1.02;

    color: #37473b;
}

.hero-content h1 span {
    display: block;

    color: #5f8168;
    font-style: italic;
}

.hero-content p {
    max-width: 360px;

    margin: 26px 0 34px;

    font-size: 15px;
    line-height: 1.8;

    color: #647063;
}

.hero-button {
    display: inline-block;

    padding: 14px 30px;

    border-radius: 3px;

    background: #435a48;

    color: #fdfdfb;
    font-size: 12px;
    letter-spacing: .3px;

    text-decoration: none;

    transition: background .2s ease, transform .2s ease;
}

.hero-button:hover {
    background: #344739;
    transform: translateY(-1px);
}

.section-container {
    width: min(1200px, calc(100% - 100px));
    margin: 0 auto;

    display: grid;
    grid-template-columns: minmax(0, 1fr) 460px;
    align-items: center;
    gap: 70px;
}

.section-label {
    display: inline-block;

    margin-bottom: 14px;

    color: #6d9376;
    font-size: 11px;
    letter-spacing: 2.5px;
}

.section-heading {
    max-width: 620px;

    margin: 0 auto 56px;

    text-align: center;
}

.section-heading h2,
.about-content h2,
.contact-heading h2 {
    margin: 0;

    font-family: Georgia, 'Times New Roman', serif;
    font-size: 40px;
    font-weight: 400;
    line-height: 1.25;

    color: #3c473e;
}

.section-heading h2 span,
.about-content h2 span,
.contact-heading h2 span {
    color: #5f8168;
    font-style: italic;
}

.section-heading p {
    margin-top: 18px;

    font-size: 14px;
    line-height: 1.8;

    color: #7a847b;
}

.about-section {
    min-height: 700px;

    padding: 110px 0;

    background: #fdfdfb;

    overflow: hidden;
}

.about-section .section-container {
    width: 90%;
    max-width: 1240px;

    margin: 0 auto;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 80px;
}

.about-content {
    width: 46%;
}

.about-content .section-label {
    display: block;

    margin-bottom: 24px;
}

.about-content h2 {
    margin: 0 0 34px;
    font-size: 46px;
    line-height: 1.2;
}

.about-content p {
    max-width: 480px;

    margin: 0 0 22px;

    font-size: 15px;
    line-height: 1.9;

    color: #7a847b;
}

.text-button {
    display: inline-flex;
    align-items: center;

    gap: 12px;

    margin-top: 14px;

    color: #4a6b52;
    font-size: 13px;
    letter-spacing: .2px;

    text-decoration: none;
}

.text-button span {
    font-size: 16px;
    transition: transform .2s ease;
}

.text-button:hover span {
    transform: translateX(4px);
}

.about-visual {
    width: 48%;
    min-height: 400px;

    display: flex;
    align-items: center;
    justify-content: center;
}

.visual-card {
    position: relative;

    width: 400px;
    max-width: 100%;
    aspect-ratio: 4 / 5;

    overflow: hidden;

    border-radius: 4px;

    background: #edf3ea;

    box-shadow: 0 24px 60px rgba(60, 80, 62, .12);
}

.visual-card::after {
    content: '';

    position: absolute;
    inset: 0;

    border: 1px solid rgba(255, 255, 255, .35);
}

.visual-card img {
    width: 100%;
    height: 100%;

    object-fit: cover;
    object-position: center;
}

.features-section {
    padding: 110px 5%;

    background: #f2f6f0;
}

.features-grid {
    width: min(1020px, 100%);

    margin: auto;

    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 26px;
}

.feature-card {
    position: relative;

    min-height: 250px;

    padding: 38px 34px;

    background: #fdfdfb;

    border: 1px solid #e6ebe3;
    border-radius: 4px;

    transition: box-shadow .25s ease, transform .25s ease;
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 40px rgba(60, 80, 62, .08);
}

.feature-number {
    position: absolute;
    top: 24px;
    right: 26px;

    color: #cddbc9;
    font-family: Georgia, serif;
    font-size: 13px;
}

.feature-icon {
    width: 42px;
    height: 42px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 26px;

    border-radius: 50%;

    background: #e9f1e6;

    color: #4a6b52;
    font-size: 18px;
}

.feature-card h3 {
    margin: 0 0 12px;

    font-family: Georgia, serif;
    font-size: 21px;
    font-weight: 400;

    color: #3c473e;
}

.feature-card p {
    margin: 0;

    font-size: 13px;
    line-height: 1.85;

    color: #808a81;
}

.products-section {
    padding: 110px 5%;

    background: #fdfdfb;
}

.products-grid {
    width: min(1080px, 100%);
    margin: auto;

    display: flex;
    gap: 26px;

    overflow-x: auto;
    overflow-y: hidden;

    padding: 8px 5px 28px;

    scroll-behavior: smooth;
    scroll-snap-type: x mandatory;

    scrollbar-width: thin;
    scrollbar-color: #bfd3ba transparent;
}

.product-card {
    flex: 0 0 300px;

    overflow: hidden;

    border: 1px solid #e6ebe3;
    border-radius: 4px;

    background: #fdfdfb;

    transition: box-shadow .25s ease, transform .25s ease;

    scroll-snap-align: start;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 40px rgba(60, 80, 62, .08);
}

.product-image {
    height: 320px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #f2f6f0;

    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;

    object-fit: cover;
    display: block;

    transition: transform .4s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-info {
    padding: 24px 22px 26px;
}

.product-category {
    color: #6d9376;
    font-size: 10px;
    letter-spacing: 1.5px;
}

.product-info h3 {
    margin: 10px 0 8px;

    font-family: Georgia, serif;
    font-size: 20px;
    font-weight: 400;

    color: #3c473e;
}

.product-info p {
    margin: 0;

    font-size: 12px;
    line-height: 1.7;

    color: #808a81;
}


.contact-section {
    padding: 110px 5%;

    background: #f2f6f0;
}

.contact-container {
    width: min(1020px, 100%);

    margin: auto;
}

.contact-heading {
    max-width: 600px;

    margin-bottom: 60px;
}

.contact-heading p {
    max-width: 480px;

    margin-top: 20px;

    font-size: 14px;
    line-height: 1.85;

    color: #7a847b;
}

.contact-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
}

.contact-item {
    padding: 30px 26px;

    background: #fdfdfb;

    border: 1px solid #e6ebe3;
    border-radius: 4px;
}

.contact-label {
    color: #6d9376;
    font-size: 10px;
    letter-spacing: 1.5px;
}

.contact-item h3 {
    margin: 12px 0 0;

    font-size: 14px;
    font-weight: 400;

    color: #4a544b;
}

.footer {
    padding: 44px 20px;

    text-align: center;

    background: #fdfdfb;

    border-top: 1px solid #e6ebe3;
}

.footer-logo img {
    width: 56px;
    height: 56px;

    object-fit: contain;

    mix-blend-mode: multiply;
}

.footer p {
    margin: 12px 0 0;

    color: #a3aca4;
    font-size: 11px;
    letter-spacing: .2px;
}

@media (max-width: 1024px) {

    .section-container {
        grid-template-columns: 1fr;
        gap: 40px;
    }

    .about-section .section-container {
        flex-direction: column;
    }

    .about-content,
    .about-visual {
        width: 100%;
    }

    .about-visual {
        justify-content: center;
    }
}

@media (max-width: 800px) {

    .navbar {
        padding: 0 20px;
    }

    .nav-menu {
        gap: 18px;
    }

    .nav-menu a {
        font-size: 11px;
    }

    .hero-content {
        width: 82%;

        padding-left: 24px;
    }

    .hero-content h1 {
        font-size: 44px;
    }

    .hero-content p {
        font-size: 13px;
    }

    .about-content h2 {
        font-size: 34px;
    }

    .features-grid,
    .contact-grid {
        grid-template-columns: 1fr;
    }

    .products-grid {
        gap: 18px;
    }

    .product-card {
        flex-basis: 78%;
    }
}

@media (max-width: 480px) {

    .hero {
        min-height: 540px;
    }

    .hero-content {
        width: 100%;

        padding-left: 20px;
        padding-right: 20px;
    }

    .hero-content h1 {
        font-size: 36px;
    }

    .section-heading h2,
    .about-content h2,
    .contact-heading h2 {
        font-size: 30px;
    }
}

</style>