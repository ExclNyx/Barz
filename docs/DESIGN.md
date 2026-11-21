# DESIGN.md - Barz Barbershop

## Identity
- **Product**: Barz Barbershop Booking System
- **Tagline**: Book, datang, kelihatan keren.
- **Audience**: Pelajar dan mahasiswa, pria 17-28 tahun, budget-conscious tapi peduli penampilan
- **Personality**: Casual-cool. Santai tapi rapi. Tidak arogan, tidak murahan. Seperti teman yang kebetulan jago potong rambut.

## Visual Language

### Palette (core: moss green primary + cafe noir dark + taupe warmth)
| Role | Name | Hex | Rationale |
|------|------|-----|-----------|
| Background | Warm Off-White | `#F9F7F2` | Hangat, tidak steril, lembut di mata |
| Surface | Cream | `#FFF8F0` | Card background, section alternate |
| Text primary | Kombu Green | `#354024` | Deep, rich green - teks utama, cukup gelap untuk readability |
| Text secondary | Warm Gray | `#5A5A5A` | Body text, captions |
| Accent | Moss Green | `#889063` | PRIMARY. Tombol utama, link, highlight. Earthy, satu aksen, tidak tersebar |
| Accent hover | Dark Moss | `#767E54` | Hover state untuk aksen |
| Secondary Accent | Taupe | `#C9A876` | Detail kecil, dekoratif, warmth |
| Dark accent | Cafe Noir | `#4C3D19` | Dark cards, booking summary, footer social. Bukan karakter dark mode - hanya elemen gelap terpilih |
| Success | Muted Green | `#6FA86F` | Confirmed booking, success state |
| Error | Warm Rust | `#D97F6E` | Error, cancelled state |
| Border | Tan | `#CFBB99` | Separator, input border - warmer dari sebelumnya |

> Moss green (`#889063`) adalah warna primer untuk tombol, link, dan highlight. Cafe noir (`#4C3D19`) hanya untuk elemen gelap terpilih: booking summary, CTA band alternatif, footer social. Bukan dark mode default.

### Typography
- System font stack, no external fonts. Rationale: nol dependensi, cepat, konsisten lintas OS.
- **Heading**: system stack bold, `letter-spacing: -.01em`
- **Body**: system stack regular, readable, approachable
- **Accent/Label**: medium, spasi normal. Tidak ada uppercase ekstrem.

### Spacing & Radius
- **Base unit**: 4px
- **Section padding**: 80px vertical desktop, 48px mobile
- **Card radius**: 16px (friendly, approachable)
- **Button radius**: 16px (tegas, tidak pill-shaped)
- **Input radius**: 4px

### Shadows
- Card: `0 2px 8px rgba(0, 0, 0, 0.04)` - subtle, tidak floating
- Elevated (modal, dropdown): `0 8px 24px rgba(0, 0, 0, 0.14)` - jelas hierarchy

### Borders
- Default: `1px solid #CFBB99` (tan)
- Focus: `1px solid #889063` (moss) - visible, accessible

## Dials
- **ENERGY**: 2 (balanced - confident tapi tidak agresif)
- **RHYTHM**: 2 (consistent dengan beberapa variasi antar section)
- **MOTION**: 1 (hover states, subtle transitions 200ms, marquee pause-on-hover. Tidak ada animasi berlebihan)

## Identity Motif
Satu motif berulang: garis horizontal tipis (`1px solid #CFBB99`) sebagai pemisah section dan elemen. Bukan kartu mengambang, bukan shadow besar - melainkan struktur yang bersih dari garis pembagi. Memberi kesan barbershop yang neat dan terorganisir.

## Component Voice
- **Tombol utama**: moss solid, teks cream/putih, radius 16px. Satu per section. Tidak semua hal jadi tombol besar.
- **Tombol sekunder**: transparent + border moss, teks moss. Lebih lightweight.
- **Badge status**: tanpa glow, tanpa pill. Kotak kecil dengan warna solid tipis.
- **Icon**: inline SVG stroke (Lucide-style paths), hanya yang relevan dengan konten.

## What to Avoid
- Gradient biru-ungu atau warna yang tidak ada dalam palette
- Dark mode default (bukan karakter brand ini)
- Pill-shaped di mana-mana
- Shadow besar di setiap card
- Animasi fade-up di setiap elemen
- Testimonial atau statistik yang tidak nyata (R-17, R-18)
- Tombol dengan teks "Get Started" atau "Explore" (R-15)
- Copywriting dengan kata "Seamless", "Revolutionary", dll (R-16)
- External fonts, Bootstrap, CDN links, external images. Vanilla CSS + system fonts + inline SVG only.

## Tone of Voice
- Bahasa Indonesia, natural, tidak kaku
- Tidak menggurui, tidak berlebihan
- Spesifik: "Pilih jadwal kapan saja, hari ini juga" bukan "Seamless booking experience"
