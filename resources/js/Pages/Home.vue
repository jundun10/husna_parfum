<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const logoUrl = '/images/logo.jpg';
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
                href="/dashboard"
                class="logo"
            >
                <img
                    :src="logoUrl"
                    alt="Lamore Perfumes"
                >
            </Link>

            <div class="nav-menu">
                <a href="#beranda">Beranda</a>
                <a href="#tentang">Tentang Kami</a>
                <a href="#keunggulan">Keunggulan</a>
                <a href="#produk">Produk</a>
                <a href="#kontak">Kontak</a>
            </div>

            <button
                class="logout-button"
                @click="logout"
            >
                Logout
            </button>

        </nav>


        
        <main id="beranda">

            
<section class="hero" id="beranda">

    <img
        :src="heroUrl"
        alt="Lamore Perfumes"
        class="hero-image"
    >

    <div class="hero-content">

        <span class="hero-label">
            WELCOME TO
        </span>

        <h1>
            Lamore
            <span>Perfumes</span>
        </h1>

        <p>
            Temukan aroma yang menggambarkan
            karakter dan kepribadianmu.
        </p>

        <a
            href="#produk"
            class="hero-button"
        >
            Lihat Koleksi
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
                Lamore Perfumes hadir untuk memberikan
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


        <div class="about-visual">

            <div class="visual-card">

                <img
                    :src="aboutParfumUrl"
                    alt="Koleksi Lamore Perfumes"
                >

            </div>

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
                        <span>Lamore?</span>
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
                        Jelajahi pilihan parfum Lamore yang
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
                            Jangan ragu untuk menghubungi Lamore Perfumes.
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
                                Indonesia
                            </h3>

                        </div>

                    </div>

                </div>

            </section>


        </main>

        <footer class="footer">

            <div class="footer-logo">
                <img
                    :src="logoUrl"
                    alt="Lamore Perfumes"
                >
            </div>

            <p>
                © 2026 Lamore Perfumes.
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
    font-family: Arial, Helvetica, sans-serif;
    color: #666;
}


/* =========================
   PAGE
========================= */

.home-page {
    background: #ffffff;
}


/* =========================
   NAVBAR
========================= */

.navbar {
    position: sticky;
    top: 0;
    z-index: 100;

    height: 64px;

    display: flex;
    align-items: center;

    padding: 0 28px;

    background: rgba(239, 250, 250, .96);

    border-bottom: 1px solid #dceeee;

    backdrop-filter: blur(8px);
}

.logo {
    display: flex;
    align-items: center;

    width: 60px;
    height: 64px;

    text-decoration: none;
}

.logo img {
    width: 58px;
    height: 58px;

    object-fit: contain;

    mix-blend-mode: multiply;
}

.nav-menu {
    flex: 1;

    display: flex;
    justify-content: center;

    gap: 38px;
}

.nav-menu a {
    position: relative;

    color: #777;

    font-size: 10px;

    text-decoration: none;

    transition: .2s;
}

.nav-menu a:hover {
    color: #6daa5c;
}

.logout-button {
    border: none;

    background: transparent;

    color: #777;

    font-size: 10px;

    cursor: pointer;

    transition: .2s;
}

.logout-button:hover {
    color: #6daa5c;
}


/* =========================
   HERO
========================= */

.hero {
    position: relative;

    min-height: 560px;

    overflow: hidden;

    display: flex;
    align-items: center;

    background: #dcefe4;
}

.hero-image {
    position: absolute;

    inset: 0;

    width: 100%;
    height: 100%;

    object-fit: cover;

    object-position: center;
}

.hero-content {
    position: relative;

    z-index: 2;

    width: 48%;

    padding-left: 8%;

    color: #555;
}

.hero-label {
    display: block;

    margin-bottom: 15px;

    color: #628c70;

    font-size: 10px;

    letter-spacing: 4px;
}

.hero-content h1 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 58px;

    line-height: .95;

    font-weight: normal;

    color: #4f6256;
}

.hero-content h1 span {
    display: block;

    color: #628c70;

    font-style: italic;
}

.hero-content p {
    max-width: 350px;

    margin: 25px 0;

    font-size: 12px;

    line-height: 1.9;

    color: #64746a;
}

.hero-button {
    display: inline-block;

    padding: 12px 25px;

    border-radius: 6px;

    background: #628c70;

    color: white;

    font-size: 10px;

    text-decoration: none;

    transition: .2s;
}

.hero-button:hover {
    background: #4f765c;
}


/* =========================
   GENERAL
========================= */

.section-container {
    width: min(1200px, calc(100% - 100px));
    margin: 0 auto;

    display: grid;
    grid-template-columns: minmax(0, 1fr) 460px;
    align-items: center;
    gap: 60px;
}

.section-label {
    display: inline-block;

    margin-bottom: 13px;

    color: #7fbd6c;

    font-size: 9px;

    letter-spacing: 3px;
}

.section-heading {
    max-width: 650px;

    margin: 0 auto 50px;

    text-align: center;
}

.section-heading h2,
.about-content h2,
.contact-heading h2 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 38px;

    font-weight: normal;

    line-height: 1.2;

    color: #555;
}

.section-heading h2 span,
.about-content h2 span,
.contact-heading h2 span {
    color: #7fbd6c;

    font-style: italic;
}

.section-heading p {
    margin-top: 18px;

    font-size: 11px;

    line-height: 1.8;

    color: #888;
}


/* =========================
   TENTANG KAMI
========================= */

.about-section {
    min-height: 720px;

    padding: 90px 0;

    background: #ffffff;

    overflow: hidden;
}

.about-section .section-container {
    width: 90%;
    max-width: 1280px;

    margin: 0 auto;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 70px;
}


/* =========================
   KONTEN KIRI
========================= */

.about-content {
    width: 48%;

    padding-left: 10px;
}

.about-content .section-label {
    display: block;

    margin-bottom: 30px;

    color: #69ad5b;

    font-size: 13px;

    letter-spacing: 5px;

    font-weight: 500;
}

.about-content h2 {
    margin: 0 0 38px;

    font-family: Georgia, 'Times New Roman', serif;

    font-size: 58px;

    line-height: 1.15;

    font-weight: 400;

    color: #555;
}

.about-content h2 span {
    color: #69ad5b;

    font-style: italic;
}

.about-content p {
    max-width: 520px;

    margin: 0 0 25px;

    color: #888;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 16px;

    line-height: 1.9;
}


/* =========================
   TOMBOL KENALI KAMI
========================= */

.text-button {
    display: inline-flex;

    align-items: center;

    gap: 18px;

    margin-top: 18px;

    color: #69ad5b;

    font-size: 14px;

    text-decoration: none;
}

.text-button span {
    font-size: 22px;
}


.about-visual {
    width: 50%;
    min-height: 420px;

    display: flex;
    align-items: center;
    justify-content: flex-end;

    padding-right: 30px;
}

.visual-card {
    width: 390px;
    height: 390px;

    border-radius: 50%;

    overflow: hidden;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #edf6ee;

    box-shadow:
        0 20px 50px rgba(100, 130, 100, 0.08);
}

.visual-card img {
    width: 100%;
    height: 100%;

    object-fit: cover;
    object-position: center;
}
/* =========================
   FEATURES
========================= */

.features-section {
    padding: 100px 5%;

    background: #f7faf7;
}

.features-grid {
    width: min(1000px, 100%);

    margin: auto;

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 22px;
}

.feature-card {
    position: relative;

    min-height: 270px;

    padding: 32px;

    background: #ffffff;

    border: 1px solid #e7eee7;

    border-radius: 10px;

    transition: .25s;
}

.feature-card:hover {
    transform: translateY(-5px);

    box-shadow:
        0 15px 35px rgba(90,120,90,.08);
}

.feature-number {
    position: absolute;

    top: 20px;
    right: 22px;

    color: #d6e4d5;

    font-size: 11px;
}

.feature-icon {
    width: 45px;
    height: 45px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 25px;

    border-radius: 50%;

    background: #edf7ec;

    color: #7fbd6c;

    font-size: 20px;
}

.feature-card h3 {
    margin: 0 0 12px;

    font-family: Georgia, serif;

    font-size: 20px;

    font-weight: normal;

    color: #555;
}

.feature-card p {
    margin: 0;

    font-size: 10px;

    line-height: 1.9;

    color: #888;
}


/* =========================
   PRODUCTS
========================= */

.products-section {
    padding: 100px 5%;

    background: #ffffff;
}

.products-grid {
    width: min(1050px, 100%);
    margin: auto;

    display: flex;
    gap: 25px;

    overflow-x: auto;
    overflow-y: hidden;

    padding: 10px 5px 25px;

    scroll-behavior: smooth;
    scroll-snap-type: x mandatory;

    scrollbar-width: thin;
    scrollbar-color: #b8d6b0 transparent;
}

.product-card {
    flex: 0 0 320px;

    overflow: hidden;

    border: 1px solid #e8ece8;
    border-radius: 10px;

    background: white;

    transition: .2s;

    scroll-snap-align: start;
}

.product-card:hover {
    transform: translateY(-5px);

    box-shadow:
        0 15px 35px rgba(90,120,90,.08);
}

.product-image {
    height: 300px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #f1f6f1;

    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;

    object-fit: cover;

    display: block;

    transition: transform .35s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.04);
}

.product-info {
    padding: 23px;
}

.product-category {
    color: #7fbd6c;

    font-size: 8px;

    letter-spacing: 2px;
}

.product-info h3 {
    margin: 8px 0;

    font-family: Georgia, serif;

    font-size: 20px;

    font-weight: normal;

    color: #555;
}

.product-info p {
    margin: 0;

    font-size: 10px;

    color: #888;
}


/* =========================
   CONTACT
========================= */

.contact-section {
    padding: 100px 5%;

    background: #f3f8f3;
}

.contact-container {
    width: min(1000px, 100%);

    margin: auto;
}

.contact-heading {
    max-width: 600px;

    margin-bottom: 55px;
}

.contact-heading p {
    max-width: 480px;

    margin-top: 20px;

    font-size: 11px;

    line-height: 1.9;

    color: #888;
}

.contact-grid {
    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 20px;
}

.contact-item {
    padding: 25px;

    background: white;

    border-radius: 8px;
}

.contact-label {
    color: #7fbd6c;

    font-size: 8px;

    letter-spacing: 2px;
}

.contact-item h3 {
    margin: 10px 0 0;

    font-size: 11px;

    font-weight: normal;

    color: #666;
}


/* =========================
   FOOTER
========================= */

.footer {
    padding: 35px 20px;

    text-align: center;

    background: #ffffff;

    border-top: 1px solid #e7eee7;
}

.footer-logo img {
    width: 65px;
    height: 65px;

    object-fit: contain;

    mix-blend-mode: multiply;
}

.footer p {
    margin: 10px 0 0;

    color: #aaa;

    font-size: 9px;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 800px) {

    .nav-menu {
        gap: 15px;
    }

    .nav-menu a {
        font-size: 8px;
    }

    .hero-content {
        width: 70%;

        padding-left: 35px;
    }

    .hero-content h1 {
        font-size: 45px;
    }

    .about-content,
    .about-visual {
        width: 100%;
    }

    .about-content {
        padding-right: 0;
    }

    .about-visual {
        margin-top: 50px;
    }

    .features-grid,
    .products-grid,
    .contact-grid {
        grid-template-columns: 1fr;
    }
}

</style>