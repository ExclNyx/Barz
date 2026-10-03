<?php
?>
<style type="text/tailwindcss">
@import "tailwindcss";

@layer base, components;

@layer base {
  *,*::before,*::after {
    @apply [box-sizing:border-box] [margin:0] [padding:0];
  }
  html {
    @apply [font-size:16px];
  }
  body {
    @apply [font-family:-apple-system,BlinkMacSystemFont,"Segoe_UI",Roboto,"Helvetica_Neue",Arial,"Noto_Sans",sans-serif] [background:#F9F7F2] [color:#354024] [line-height:1.6] [-webkit-font-smoothing:antialiased];
  }
  h1,h2,h3,h4 {
    @apply [font-family:-apple-system,BlinkMacSystemFont,"Segoe_UI",Roboto,"Helvetica_Neue",Arial,"Noto_Sans",sans-serif] [font-weight:700] [line-height:1.25] [letter-spacing:-.01em];
  }
  a {
    @apply [color:#889063];
  }
  img {
    @apply [max-width:100%] [display:block];
  }
  button {
    @apply [font-family:-apple-system,BlinkMacSystemFont,"Segoe_UI",Roboto,"Helvetica_Neue",Arial,"Noto_Sans",sans-serif];
  }
  svg.ic {
    @apply [width:20px] [height:20px] [flex:none] [fill:none] [stroke:currentColor] [stroke-width:2] [stroke-linecap:round] [stroke-linejoin:round];
  }
}

@layer components {
  .wrap {
    @apply [max-width:1120px] [margin:0_auto] [padding-left:24px] [padding-right:24px];
  }
  .topbar {
    @apply [position:sticky] [top:0] [z-index:50] [background:rgba(249,247,242,.92)] [-webkit-backdrop-filter:blur(12px)] [backdrop-filter:blur(12px)] [border-bottom:1px_solid_#CFBB99];
  }
  .topbar-in {
    @apply [display:flex] [align-items:center] [justify-content:space-between] [gap:12px] [padding-top:14px] [padding-bottom:14px];
  }
  .brand {
    @apply [display:inline-flex] [align-items:center] [gap:8px] [text-decoration:none] [color:#354024] [font-weight:800] [font-size:1.35rem] [letter-spacing:.12em];
  }
  .brand svg.ic {
    @apply [color:#889063] [width:22px] [height:22px];
  }
  .site-links {
    @apply [display:flex] [align-items:center] [gap:4px];
  }
  .site-links a {
    @apply [position:relative] [text-decoration:none] [color:#354024] [font-size:.92rem] [font-weight:500] [padding:8px_14px_10px] [border-radius:16px];
  }
  .site-links a:hover {
    @apply [color:#889063] [background:rgba(136,144,99,.08)];
  }
  .site-links a.on {
    @apply [color:#889063] [font-weight:700];
  }
  .site-links a.on::after {
    @apply [content:""] [position:absolute] [left:14px] [right:14px] [bottom:2px] [height:2px] [background:#889063] [border-radius:2px];
  }
  .site-actions {
    @apply [display:flex] [align-items:center] [gap:8px];
  }
  .user-pill {
    @apply [display:flex] [align-items:center] [gap:8px] [background:#fff] [border:1px_solid_#CFBB99] [padding:6px_12px_6px_6px] [border-radius:12px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04)];
  }
  .user-ava {
    @apply [width:28px] [height:28px] [border-radius:50%] [background:#889063] [color:#fff] [display:flex] [align-items:center] [justify-content:center] [font-weight:700] [font-size:.8rem] [flex:none];
  }
  .user-pill span.nm {
    @apply [font-size:.85rem] [font-weight:600];
  }
  .user-pill a.lo {
    @apply [font-size:.75rem] [color:#D97F6E] [text-decoration:none];
  }
  .user-pill a.lo:hover {
    @apply [text-decoration:underline];
  }
  .iconbtn {
    @apply [display:inline-flex] [align-items:center] [justify-content:center] [width:40px] [height:40px] [border:1px_solid_transparent] [background:transparent] [border-radius:12px] [color:#354024] [cursor:pointer] [text-decoration:none];
  }
  .iconbtn:hover {
    @apply [color:#889063] [border-color:#CFBB99] [background:#FFF8F0];
  }
  .menu-toggle {
    @apply [display:none];
  }
  .mnav {
    @apply [display:none] [border-top:1px_solid_#CFBB99] [padding:8px_24px_16px];
  }
  .mnav a {
    @apply [display:block] [text-decoration:none] [color:#354024] [font-weight:500] [padding:10px_4px] [border-bottom:1px_solid_#CFBB99];
  }
  .mnav a:last-child {
    @apply [border-bottom:0];
  }
  .mnav.open {
    @apply [display:block];
  }
  .button {
    @apply [display:inline-flex] [align-items:center] [justify-content:center] [gap:8px] [padding:14px_28px] [border-radius:16px] [font-weight:600] [font-size:.95rem] [text-decoration:none] [cursor:pointer] [border:1px_solid_transparent] [transition:background_200ms,color_200ms,border-color_200ms];
  }
  .button-primary {
    @apply [background:#889063] [color:#fff];
  }
  .button-primary:hover {
    @apply [background:#767E54];
  }
  .button-outline {
    @apply [background:transparent] [color:#889063] [border-color:#889063];
  }
  .button-outline:hover {
    @apply [background:#889063] [color:#fff];
  }
  .button-dark {
    @apply [background:#354024] [color:#fff];
  }
  .button-dark:hover {
    @apply [background:#26331d];
  }
  .button-small {
    @apply [padding:9px_16px] [font-size:.85rem];
  }
  .button-block {
    @apply [width:100%];
  }
  .button[disabled] {
    @apply [opacity:.5] [cursor:not-allowed];
  }
  .button-danger-ghost {
    @apply [background:transparent] [color:#D97F6E] [border-color:#CFBB99];
  }
  .button-danger-ghost:hover {
    @apply [background:rgba(217,127,110,.08)] [border-color:#D97F6E];
  }
  .hero {
    @apply [text-align:center] [padding:72px_0_56px];
  }
  .hero h1 {
    @apply [font-size:3rem] [margin-bottom:16px];
  }
  .hero h1 .hl {
    @apply [color:#889063];
  }
  .lead {
    @apply [font-size:1.1rem] [color:#354024] [max-width:640px] [margin:0_auto_28px];
  }
  .cta-row {
    @apply [display:flex] [flex-wrap:wrap] [gap:12px] [justify-content:center];
  }
  .section {
    @apply [padding:56px_0];
  }
  .section + .section {
    @apply [border-top:1px_solid_#CFBB99];
  }
  .sec-head {
    @apply [margin-bottom:32px];
  }
  .sec-head.left {
    @apply [text-align:left];
  }
  .sec-head.center {
    @apply [text-align:center];
  }
  .sec-title {
    @apply [font-size:1.8rem] [margin-bottom:8px];
  }
  .sec-sub {
    @apply [color:#354024];
  }
  .narrow {
    @apply [max-width:720px] [margin-left:auto] [margin-right:auto] [text-align:center];
  }
  .narrow p {
    @apply [margin-bottom:12px];
  }
  .hero2 {
    @apply [display:grid] [grid-template-columns:1fr_1fr] [gap:40px] [align-items:center] [text-align:left] [padding:72px_0_56px];
  }
  .hero2 h1 {
    @apply [font-size:3rem] [margin-bottom:16px];
  }
  .hero2 h1 .hl {
    @apply [color:#889063];
  }
  .hero2 .lead {
    @apply [margin:0_0_28px];
  }
  .hero2 .cta-row {
    @apply [justify-content:flex-start];
  }
  .hero-badge {
    @apply [display:inline-block] [padding:6px_16px] [border-radius:999px] [background:rgba(136,144,99,.15)] [border:1px_solid_rgba(136,144,99,.3)] [color:#354024] [font-size:.75rem] [font-weight:700] [letter-spacing:.08em] [text-transform:uppercase] [margin-bottom:16px];
  }
  .hero-art {
    @apply [background:#FFF8F0] [border:1px_solid_#CFBB99] [border-radius:16px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04)] [min-height:340px] [display:flex] [align-items:center] [justify-content:center] [color:#889063];
  }
  .hero-art svg.ic {
    @apply [width:96px] [height:96px] [opacity:.4];
  }
  .feat3 {
    @apply [display:grid] [grid-template-columns:repeat(3,1fr)] [gap:20px];
  }
  .feat {
    @apply [background:#FFF8F0] [border:1px_solid_#CFBB99] [border-radius:16px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04)] [padding:32px_24px] [text-align:center];
  }
  .feat-ic {
    @apply [width:56px] [height:56px] [margin:0_auto_16px] [border-radius:16px] [background:rgba(136,144,99,.12)] [color:#889063] [display:flex] [align-items:center] [justify-content:center];
  }
  .feat-ic svg.ic {
    @apply [width:26px] [height:26px];
  }
  .feat h3 {
    @apply [font-size:1.15rem] [margin-bottom:8px];
  }
  .feat p {
    @apply [font-size:.9rem] [color:#5A5A5A];
  }
  .cta-band {
    @apply [background:#354024] [color:#fff] [border-radius:16px] [padding:48px_32px] [text-align:center];
  }
  .cta-band h2 {
    @apply [margin-bottom:10px] [font-size:1.9rem];
  }
  .cta-band p {
    @apply [color:rgba(255,255,255,.7)] [margin-bottom:24px];
  }
  .cta-band .cta-row .button-outline {
    @apply [color:#fff] [border-color:rgba(255,255,255,.4)];
  }
  .cta-band .cta-row .button-outline:hover {
    @apply [background:rgba(255,255,255,.12)] [border-color:#fff];
  }
  .svc3 {
    @apply [display:grid] [grid-template-columns:repeat(3,1fr)] [gap:20px];
  }
  .svc-sec {
    @apply [margin-bottom:40px];
  }
  .svc-sec-head {
    @apply [display:flex] [align-items:center] [gap:12px] [border-bottom:1px_solid_#CFBB99] [padding-bottom:12px] [margin-bottom:20px];
  }
  .svc-sec-head svg.ic {
    @apply [width:24px] [height:24px] [color:#889063] [flex:none];
  }
  .svc-sec-head h2 {
    @apply [font-size:1.4rem] [font-weight:700] [letter-spacing:.02em];
  }
  .svc-count {
    @apply [font-size:.75rem] [background:rgba(136,144,99,.15)] [color:#354024] [padding:2px_10px] [border-radius:999px] [font-weight:700];
  }
  .svc {
    @apply [background:#fff] [border:1px_solid_#CFBB99] [border-radius:16px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04)] [overflow:hidden] [display:flex] [flex-direction:column];
  }
  .svc-img {
    @apply [height:200px] [object-fit:cover] [width:100%] [background:#F9F7F2];
  }
  .svc-img-ph {
    @apply [height:200px] [background:#F9F7F2] [display:flex] [align-items:center] [justify-content:center] [color:#889063];
  }
  .svc-img-ph svg.ic {
    @apply [width:40px] [height:40px] [opacity:.5];
  }
  .svc-body {
    @apply [padding:24px] [display:flex] [flex-direction:column] [flex:1];
  }
  .svc-body h3 {
    @apply [font-size:1.25rem] [margin-bottom:8px];
  }
  .svc-body .desc {
    @apply [font-size:.9rem] [color:#354024] [margin-bottom:16px] [flex:1];
  }
  .svc-meta {
    @apply [display:flex] [align-items:center] [justify-content:space-between] [margin-bottom:20px] [padding-bottom:16px] [border-bottom:1px_solid_#CFBB99];
  }
  .price {
    @apply [font-size:1.3rem] [font-weight:700] [color:#889063];
  }
  .dur {
    @apply [font-size:.85rem] [color:#5A5A5A] [display:inline-flex] [align-items:center] [gap:6px];
  }
  .dur svg.ic {
    @apply [width:15px] [height:15px];
  }
  .cat-tabs {
    @apply [display:flex] [gap:8px] [margin-bottom:20px] [flex-wrap:wrap];
  }
  .cat-tab {
    @apply [padding:8px_16px] [border:1px_solid_#CFBB99] [border-radius:16px] [background:#fff] [font-size:.85rem] [font-weight:600] [cursor:pointer] [transition:all_200ms] [text-decoration:none] [color:#354024];
  }
  .cat-tab:hover {
    @apply [border-color:#889063];
  }
  .cat-tab.on {
    @apply [background:#354024] [color:#fff] [border-color:#354024];
  }
  .svc-cat {
    @apply [display:inline-block] [padding:2px_8px] [border-radius:4px] [font-size:.7rem] [font-weight:700] [background:rgba(136,144,99,.15)] [color:#889063] [margin-bottom:8px] [text-transform:uppercase] [letter-spacing:.04em];
  }
  .panel {
    @apply [background:#FFF8F0] [border:1px_solid_#CFBB99] [border-radius:16px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04)];
  }
  .empty {
    @apply [padding:40px_32px] [text-align:center];
  }
  .empty svg.ic {
    @apply [width:36px] [height:36px] [color:#889063] [margin-bottom:12px];
  }
  .empty h3 {
    @apply [margin-bottom:8px];
  }
  .empty p {
    @apply [color:#354024] [margin-bottom:20px];
  }
  .invite {
    @apply [margin-top:40px] [padding:36px] [text-align:center] [max-width:720px] [margin-left:auto] [margin-right:auto];
  }
  .invite h3 {
    @apply [margin-bottom:8px];
  }
  .invite p {
    @apply [margin-bottom:20px];
  }
  .auth-wrap {
    @apply [display:flex] [justify-content:center] [padding:56px_0];
  }
  .auth-panel {
    @apply [width:100%] [max-width:460px] [background:#fff] [border:1px_solid_#CFBB99] [border-radius:16px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04)] [padding:36px];
  }
  .auth-panel.wide {
    @apply [max-width:560px];
  }
  .auth-panel .center {
    @apply [text-align:center] [margin-bottom:24px];
  }
  .auth-panel h2 {
    @apply [font-size:1.7rem] [margin-bottom:6px];
  }
  .auth-panel .hint {
    @apply [font-size:.9rem] [color:#354024];
  }
  .center {
    @apply [text-align:center];
  }
  .field {
    @apply [margin-bottom:16px];
  }
  .label {
    @apply [display:block] [font-size:.85rem] [font-weight:600] [margin-bottom:6px];
  }
  .input,.select,.area {
    @apply [width:100%] [padding:12px_14px] [border:1px_solid_#CFBB99] [border-radius:4px] [background:#fff] [color:#354024] [font-size:.95rem] [font-family:-apple-system,BlinkMacSystemFont,"Segoe_UI",Roboto,"Helvetica_Neue",Arial,"Noto_Sans",sans-serif] [transition:border-color_200ms];
  }
  .input:focus,.select:focus,.area:focus {
    @apply [outline:none] [border-color:#889063];
  }
  .input::placeholder,.area::placeholder {
    @apply [color:#5A5A5A];
  }
  .micro {
    @apply [font-size:.75rem] [color:#5A5A5A] [margin-top:4px];
  }
  .divider {
    @apply [border:0] [border-top:1px_solid_#CFBB99] [margin:24px_0];
  }
  .demo-box {
    @apply [background:rgba(136,144,99,.1)] [border:1px_solid_#CFBB99] [border-radius:12px] [padding:14px] [font-size:.85rem];
  }
  .demo-box ul {
    @apply [margin:6px_0_0_18px] [font-size:.8rem] [color:#5A5A5A];
  }
  .notice {
    @apply [border-radius:12px] [padding:14px_16px] [font-size:.9rem] [margin-bottom:20px] [display:flex] [gap:8px] [align-items:flex-start];
  }
  .notice svg.ic {
    @apply [width:18px] [height:18px] [flex:none] [margin-top:2px];
  }
  .notice-error {
    @apply [background:rgba(217,127,110,.1)] [color:#a84f3a] [border-left:4px_solid_#D97F6E];
  }
  .notice-ok {
    @apply [background:rgba(111,168,111,.12)] [color:#2d6b2d] [border-left:4px_solid_#6FA86F];
  }
  .notice-info {
    @apply [background:rgba(136,144,99,.12)] [color:#3d5a3d] [border-left:4px_solid_#889063];
  }
  .page-head {
    @apply [margin-bottom:32px];
  }
  .page-head h1 {
    @apply [font-size:2rem] [margin-bottom:6px];
  }
  .book2 {
    @apply [display:grid] [grid-template-columns:2fr_1fr] [gap:20px] [align-items:start];
  }
  .book-main {
    @apply [padding:28px];
  }
  .step {
    @apply [margin-bottom:32px];
  }
  .step:last-child {
    @apply [margin-bottom:0];
  }
  .step-head {
    @apply [display:flex] [align-items:center] [gap:12px] [margin-bottom:16px];
  }
  .step-num {
    @apply [width:32px] [height:32px] [border-radius:50%] [background:#889063] [color:#fff] [display:inline-flex] [align-items:center] [justify-content:center] [font-weight:700] [font-size:.85rem] [flex:none];
  }
  .step-head h3 {
    @apply [font-size:1.2rem];
  }
  .book-sec {
    @apply [margin-bottom:20px];
  }
  .book-sec-t {
    @apply [font-size:1rem] [margin-bottom:12px] [padding-bottom:8px] [border-bottom:1px_solid_#CFBB99];
  }
  .pick-grid {
    @apply [display:grid] [grid-template-columns:1fr_1fr] [gap:12px];
  }
  .pick {
    @apply [cursor:pointer];
  }
  .pick input {
    @apply [position:absolute] [opacity:0] [pointer-events:none];
  }
  .pick-box {
    @apply [padding:18px] [border:1px_solid_#CFBB99] [border-radius:16px] [transition:border-color_200ms,background_200ms] [background:#fff];
  }
  .pick-box:hover {
    @apply [border-color:#767E54];
  }
  .pick input:checked + .pick-box {
    @apply [border-color:#889063] [background:rgba(136,144,99,.07)];
  }
  .pick-box h4 {
    @apply [margin-bottom:4px];
  }
  .pick-box .desc {
    @apply [font-size:.78rem] [color:#5A5A5A] [margin-bottom:10px];
  }
  .booking-venue-header {
    @apply [text-align:center] [margin-bottom:24px];
  }
  .venue-title {
    @apply [font-size:1.4rem] [font-weight:800] [color:#0f172a] [letter-spacing:-0.01em] [margin-bottom:4px];
  }
  .venue-location {
    @apply [display:inline-flex] [align-items:center] [gap:6px] [font-size:0.86rem] [color:#64748b] [font-weight:500];
  }
  .venue-location svg.ic {
    @apply [width:16px] [height:16px] [color:#94a3b8];
  }
  .datetime-picker {
    @apply [background:transparent] [padding:0] [max-width:460px] [margin:0_auto];
  }
  .cal-panel {
    @apply [margin-bottom:20px];
  }
  .cal-card {
    @apply [background:#ffffff] [border-radius:16px] [border:1px_solid_#eef2f6] [box-shadow:0_4px_20px_-2px_rgba(0,_0,_0,_0.05)] [padding:24px_20px];
  }
  .cal-head {
    @apply [margin-bottom:16px];
  }
  .cal-nav {
    @apply [display:flex] [align-items:center] [justify-content:space-between] [padding:0_6px];
  }
  .cal-nav-btn {
    @apply [width:34px] [height:34px] [background:#ffffff] [border:1px_solid_#e2e8f0] [cursor:pointer] [color:#64748b] [border-radius:8px] [display:inline-flex] [align-items:center] [justify-content:center] [transition:all_150ms_ease];
  }
  .cal-nav-btn:hover:not(:disabled) {
    @apply [background:#f8fafc] [color:#0f172a] [border-color:#cbd5e1];
  }
  .cal-nav-btn:disabled {
    @apply [opacity:0.25] [cursor:not-allowed];
  }
  .cal-nav-btn svg.ic {
    @apply [width:16px] [height:16px];
  }
  .cal-title {
    @apply [font-size:1.05rem] [font-weight:500] [color:#334155] [letter-spacing:-0.01em] [display:flex] [align-items:center] [gap:12px];
  }
  .cal-title .cal-month {
    @apply [font-weight:500];
  }
  .cal-title .cal-year {
    @apply [font-weight:700] [color:#0f172a];
  }
  .cal-weekdays {
    @apply [display:grid] [grid-template-columns:repeat(7,_1fr)] [margin-bottom:8px];
  }
  .cal-weekday {
    @apply [text-align:center] [font-size:0.85rem] [font-weight:600] [color:#1e293b] [padding:8px_0];
  }
  .cal7 {
    @apply [display:grid] [grid-template-columns:repeat(7,_1fr)] [gap:4px];
  }
  .cal-day {
    @apply [width:38px] [height:38px] [margin:0_auto] [border:0] [border-radius:8px] [background:transparent] [text-align:center] [cursor:pointer] [font-size:0.92rem] [font-weight:500] [color:#334155] [display:flex] [align-items:center] [justify-content:center] [transition:all_150ms_ease] [font-family:-apple-system,BlinkMacSystemFont,"Segoe_UI",Roboto,"Helvetica_Neue",Arial,"Noto_Sans",sans-serif];
  }
  .cal-day:hover:not(.past):not(.other-month) {
    @apply [background:#f1f5f9] [color:#0f172a];
  }
  .cal-day.other-month {
    @apply [color:#cbd5e1_!important] [pointer-events:none];
  }
  .cal-day.past {
    @apply [color:#cbd5e1_!important] [pointer-events:none];
  }
  .cal-day.today {
    @apply [border:1.5px_solid_#0ea5e9] [color:#0ea5e9] [font-weight:700];
  }
  .cal-day.selected {
    @apply [background:#0ea5e9_!important] [color:#ffffff_!important] [font-weight:700] [border-radius:10px] [box-shadow:0_4px_12px_rgba(14,_165,_233,_0.4)];
  }
  .cal-hint {
    @apply [color:#94a3b8] [font-size:0.85rem] [font-style:italic] [text-align:center] [margin:20px_0_14px];
  }
  .slots-legend {
    @apply [display:flex] [align-items:center] [justify-content:center] [gap:28px] [margin-bottom:20px];
  }
  .legend-item {
    @apply [display:inline-flex] [align-items:center] [gap:8px] [font-size:0.85rem] [font-weight:500] [color:#334155];
  }
  .legend-dot {
    @apply [width:18px] [height:18px] [border-radius:5px] [display:inline-block] [flex:none];
  }
  .legend-dot.available {
    @apply [background:#ffffff] [border:1.5px_dashed_#94a3b8];
  }
  .legend-dot.reserved {
    @apply [background:#142239] [border:1.5px_solid_#142239];
  }
  .slots-placeholder {
    @apply [text-align:center] [padding:36px_16px] [background:#fff] [border-radius:12px] [border:1px_dashed_#cbd5e1] [color:#64748b];
  }
  .slots-placeholder svg.ic {
    @apply [width:32px] [height:32px] [margin:0_auto_8px] [display:block] [color:#94a3b8];
  }
  .slots-placeholder p {
    @apply [margin:0] [font-size:0.88rem];
  }
  .slots-grid-5col {
    @apply [display:grid] [grid-template-columns:repeat(5,_1fr)] [gap:8px];
  }
  .slot-btn {
    @apply [height:42px] [padding:0_4px] [border-radius:8px] [font-size:0.82rem] [font-weight:600] [display:flex] [align-items:center] [justify-content:center] [font-family:-apple-system,BlinkMacSystemFont,"Segoe_UI",Roboto,"Helvetica_Neue",Arial,"Noto_Sans",sans-serif] [transition:all_150ms_ease] [letter-spacing:0.02em];
  }
  .slot-btn.available {
    @apply [background:#ffffff] [border:1.5px_dashed_#cbd5e1] [color:#1e293b] [cursor:pointer] [box-shadow:0_1px_2px_rgba(0,_0,_0,_0.03)];
  }
  .slot-btn.available:hover {
    @apply [border-color:#0ea5e9] [border-style:solid] [color:#0ea5e9] [background:#f0f9ff] [transform:translateY(-1px)] [box-shadow:0_3px_8px_rgba(14,_165,_233,_0.18)];
  }
  .slot-btn.reserved {
    @apply [background:#142239] [border:1px_solid_#142239] [color:#ffffff] [cursor:not-allowed] [opacity:0.98] [box-shadow:0_1px_3px_rgba(20,_34,_57,_0.2)];
  }
  .slot-btn.selected {
    @apply [background:#0ea5e9_!important] [border:1.5px_solid_#0ea5e9_!important] [color:#ffffff_!important] [font-weight:700] [cursor:pointer] [transform:scale(1.02)] [box-shadow:0_3px_12px_rgba(14,_165,_233,_0.45)];
  }
  .sum {
    @apply [background:#4C3D19] [color:#ffffff] [border-radius:16px] [box-shadow:0_8px_24px_rgba(0,0,0,0.14)] [padding:28px] [position:sticky] [top:88px];
  }
  .sum h3 {
    @apply [color:#ffffff] [font-size:1.25rem] [margin-bottom:16px] [padding-bottom:12px] [border-bottom:1px_solid_rgba(255,_255,_255,_0.15)];
  }
  .sum-row {
    @apply [display:flex] [justify-content:space-between] [gap:12px] [font-size:0.9rem] [margin-bottom:12px];
  }
  .sum-row .k {
    @apply [color:#CFBB99];
  }
  .sum-row .v {
    @apply [color:#ffffff] [font-weight:700] [text-align:right];
  }
  .sum-total {
    @apply [display:flex] [justify-content:space-between] [margin-top:16px] [padding-top:16px] [border-top:1px_solid_rgba(255,_255,_255,_0.2)] [font-size:1.1rem];
  }
  .sum-total span {
    @apply [color:#ffffff] [font-weight:600];
  }
  .sum-total .v {
    @apply [color:#C9A876] [font-weight:800];
  }
  .sum .idle {
    @apply [color:rgba(255,_255,_255,_0.6)] [font-size:0.88rem];
  }
  .mygrid {
    @apply [display:grid] [grid-template-columns:1fr_1fr] [gap:20px];
  }
  .book {
    @apply [background:#fff] [border:1px_solid_#CFBB99] [border-radius:16px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04)] [display:flex] [flex-direction:column] [overflow:hidden];
  }
  .book:hover {
    @apply [border-color:rgba(136,144,99,.4)];
  }
  .book-head {
    @apply [padding:18px_20px] [border-bottom:1px_solid_#CFBB99] [background:rgba(249,247,242,.6)] [display:flex] [justify-content:space-between] [align-items:center] [gap:10px];
  }
  .book-head strong {
    @apply [font-size:.95rem];
  }
  .book-body {
    @apply [padding:20px] [flex:1];
  }
  .kv2 {
    @apply [display:grid] [grid-template-columns:1fr_1fr] [gap:16px] [margin-bottom:16px];
  }
  .lbl {
    @apply [font-size:.75rem] [color:#5A5A5A] [display:block] [margin-bottom:2px];
  }
  .val {
    @apply [font-weight:600] [display:flex] [align-items:center] [gap:6px];
  }
  .val svg.ic {
    @apply [width:15px] [height:15px] [color:#889063];
  }
  .bill {
    @apply [font-size:1.3rem] [font-weight:700] [color:#889063] [margin-bottom:6px];
  }
  .notebox {
    @apply [background:#F9F7F2] [border:1px_solid_#CFBB99] [border-radius:12px] [padding:12px] [margin-top:12px] [font-size:.82rem];
  }
  .book-foot {
    @apply [padding:16px_20px] [border-top:1px_solid_#CFBB99] [margin-top:auto];
  }
  .warnline {
    @apply [font-size:.78rem] [color:#8a6d3b] [margin-bottom:10px] [display:flex] [gap:6px] [align-items:center];
  }
  .warnline svg.ic {
    @apply [width:15px] [height:15px];
  }
  .my-list {
    @apply [display:flex] [flex-direction:column] [gap:16px];
  }
  .book-item {
    @apply [background:#fff] [border:1px_solid_#CFBB99] [border-radius:16px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04)] [overflow:hidden];
  }
  .book-item:hover {
    @apply [border-color:rgba(136,144,99,.4)];
  }
  .book-item-head {
    @apply [display:flex] [justify-content:space-between] [align-items:flex-start] [gap:12px] [padding:18px_20px] [border-bottom:1px_solid_#CFBB99] [background:rgba(249,247,242,.6)];
  }
  .book-item-head strong {
    @apply [font-size:.95rem];
  }
  .book-item-head .tag {
    @apply [margin-top:4px];
  }
  .book-item-body {
    @apply [padding:20px];
  }
  .book-item-body .kv-row {
    @apply [display:flex] [justify-content:space-between] [gap:12px] [padding:8px_0] [border-bottom:1px_solid_#CFBB99];
  }
  .book-item-body .kv-row:last-child {
    @apply [border-bottom:0];
  }
  .book-item-body .kv-row .lbl {
    @apply [font-size:.75rem] [color:#5A5A5A] [min-width:80px] [flex:none];
  }
  .book-item-body .kv-row .val {
    @apply [font-weight:600];
  }
  .book-item-body .bill {
    @apply [font-size:1.3rem] [font-weight:700] [color:#889063] [margin-top:12px];
  }
  .book-item-foot {
    @apply [padding:16px_20px] [border-top:1px_solid_#CFBB99] [display:flex] [justify-content:space-between] [align-items:center] [gap:12px] [flex-wrap:wrap];
  }
  .auth-wrap {
    @apply [display:flex] [justify-content:center] [padding:56px_0];
  }
  .auth-card {
    @apply [width:100%] [max-width:440px] [background:#fff] [border:1px_solid_#CFBB99] [border-radius:20px] [box-shadow:0_4px_24px_rgba(0,0,0,.06)] [padding:40px_36px];
  }
  .auth-card.wide {
    @apply [max-width:480px];
  }
  .auth-card .center {
    @apply [text-align:center] [margin-bottom:28px];
  }
  .auth-card h2 {
    @apply [font-size:1.7rem] [margin-bottom:6px] [font-weight:800];
  }
  .auth-card .hint {
    @apply [font-size:.9rem] [color:#5A5A5A];
  }
  .auth-card .button-primary {
    @apply [background:#889063] [border-color:#889063];
  }
  .auth-card .button-primary:hover {
    @apply [background:#767E54] [border-color:#767E54];
  }
  .auth-card .button-block {
    @apply [width:100%];
  }
  .auth-card .divider {
    @apply [border:0] [border-top:1px_solid_#CFBB99] [margin:24px_0];
  }
  .auth-card .demo-box {
    @apply [background:rgba(136,144,99,.1)] [border:1px_solid_#CFBB99] [border-radius:12px] [padding:14px] [font-size:.85rem];
  }
  .auth-card .demo-box ul {
    @apply [margin:6px_0_0_18px] [font-size:.8rem] [color:#5A5A5A];
  }
  .tag {
    @apply [display:inline-block] [padding:4px_10px] [border-radius:4px] [font-size:.75rem] [font-weight:600];
  }
  .tag-pending {
    @apply [background:rgba(201,168,118,.18)] [color:#8a6d3b];
  }
  .tag-confirmed,.tag-completed {
    @apply [background:rgba(111,168,111,.15)] [color:#2d6a2d];
  }
  .tag-cancelled {
    @apply [background:rgba(217,127,110,.15)] [color:#a84f3a];
  }
  .tag-no_show {
    @apply [background:rgba(90,90,90,.12)] [color:#5A5A5A];
  }
  .about2 {
    @apply [display:grid] [grid-template-columns:1fr_1fr] [gap:40px] [align-items:center];
  }
  .about-feat {
    @apply [display:grid] [grid-template-columns:1fr_1fr] [gap:12px] [margin-top:20px];
  }
  .about-feat-item {
    @apply [padding:16px] [border-radius:16px] [background:#F9F7F2] [border:1px_solid_#CFBB99];
  }
  .about-feat-item strong {
    @apply [display:block] [font-size:.9rem] [margin-bottom:4px];
  }
  .about-feat-item p {
    @apply [font-size:.8rem] [color:#5A5A5A];
  }
  .about-imgs {
    @apply [display:grid] [grid-template-columns:1fr_1fr] [gap:12px];
  }
  .about-img-ph {
    @apply [background:#F9F7F2] [border:1px_solid_#CFBB99] [border-radius:16px] [min-height:220px] [display:flex] [align-items:center] [justify-content:center] [color:#889063];
  }
  .about-img-ph svg.ic {
    @apply [width:48px] [height:48px] [opacity:.4];
  }
  .about-img-ph:nth-child(2) {
    @apply [margin-top:24px];
  }
  .marquee-section {
    @apply [overflow:hidden] [border-top:1px_solid_#CFBB99] [border-bottom:1px_solid_#CFBB99] [padding:40px_0] [background:#fff];
  }
  .marquee-track {
    @apply [display:flex] [gap:20px] [width:max-content] [animation:scroll-left_30s_linear_infinite];
  }
  .marquee-track:hover {
    @apply [animation-play-state:paused];
  }
  .marquee-card {
    @apply [flex-shrink:0] [width:280px] [background:#F9F7F2] [border:1px_solid_#CFBB99] [border-radius:16px] [overflow:hidden];
  }
  .marquee-card-img {
    @apply [height:180px] [background:#FFF8F0] [display:flex] [align-items:center] [justify-content:center] [color:#889063] [font-size:3rem];
  }
  .marquee-card-img svg.ic {
    @apply [width:48px] [height:48px] [opacity:.4];
  }
  .marquee-tag {
    @apply [display:inline-block] [padding:2px_8px] [border-radius:4px] [font-size:.7rem] [font-weight:700] [background:#354024] [color:#fff] [margin-bottom:8px];
  }
  .marquee-card-body {
    @apply [padding:16px];
  }
  .marquee-card-body h4 {
    @apply [font-size:.95rem] [margin-bottom:4px];
  }
  .marquee-card-body p {
    @apply [font-size:.8rem] [color:#5A5A5A] [margin-bottom:12px];
  }
  .loc-grid {
    @apply [display:grid] [grid-template-columns:1fr_2fr] [gap:24px];
  }
  .loc-info {
    @apply [display:flex] [flex-direction:column] [gap:16px];
  }
  .loc-item {
    @apply [display:flex] [gap:10px] [font-size:.88rem];
  }
  .loc-item svg.ic {
    @apply [flex:none] [color:#889063];
  }
  .loc-map {
    @apply [border-radius:16px] [border:1px_solid_#CFBB99] [overflow:hidden] [min-height:260px] [background:#F9F7F2] [display:flex] [align-items:center] [justify-content:center] [position:relative];
  }
  .loc-map-placeholder {
    @apply [text-align:center] [padding:24px];
  }
  .loc-map-placeholder h4 {
    @apply [margin-top:8px];
  }
  .loc-map-placeholder p {
    @apply [font-size:.8rem] [color:#5A5A5A] [margin-top:4px];
  }
  .loc-badge {
    @apply [display:inline-block] [margin-top:12px] [padding:4px_12px] [border-radius:4px] [font-size:.75rem] [font-weight:600] [background:rgba(111,168,111,.15)] [color:#2d6a2d];
  }
  .chat-shell {
    @apply [background:#fff] [border:1px_solid_#CFBB99] [border-radius:16px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04)] [display:flex] [flex-direction:column] [overflow:hidden];
  }
  .chat-head {
    @apply [background:rgba(249,247,242,.7)] [border-bottom:1px_solid_#CFBB99] [padding:14px_18px] [display:flex] [align-items:center] [gap:12px];
  }
  .avatar {
    @apply [width:40px] [height:40px] [border-radius:50%] [background:#354024] [color:#fff] [display:flex] [align-items:center] [justify-content:center] [flex:none];
  }
  .avatar svg.ic {
    @apply [width:20px] [height:20px];
  }
  .chat-head strong {
    @apply [display:block] [font-size:.9rem];
  }
  .chat-head small {
    @apply [color:#5A5A5A] [font-size:.78rem];
  }
  .online-dot {
    @apply [display:inline-flex] [align-items:center] [gap:6px] [background:rgba(111,168,111,.14)] [color:#2d6a2d] [font-size:.75rem] [font-weight:700] [padding:4px_10px] [border-radius:4px];
  }
  .online-dot i {
    @apply [width:8px] [height:8px] [border-radius:50%] [background:#6FA86F] [font-style:normal];
  }
  .chat-log {
    @apply [padding:20px] [overflow-y:auto] [flex:1] [background:rgba(249,247,242,.5)] [min-height:300px] [max-height:52vh];
  }
  .msgcol {
    @apply [display:flex] [flex-direction:column] [gap:14px];
  }
  .msg {
    @apply [max-width:80%] [display:flex] [flex-direction:column];
  }
  .msg.sent {
    @apply [align-self:flex-end] [align-items:flex-end];
  }
  .msg.in {
    @apply [align-self:flex-start] [align-items:flex-start];
  }
  .bubble {
    @apply [padding:10px_16px] [border-radius:16px] [font-size:.9rem] [box-shadow:0_2px_8px_rgba(0,0,0,0.04)];
  }
  .msg.sent .bubble {
    @apply [background:#889063] [color:#fff] [border-top-right-radius:4px];
  }
  .msg.in .bubble {
    @apply [background:#fff] [border:1px_solid_#CFBB99] [border-top-left-radius:4px];
  }
  .bubble .who {
    @apply [display:block] [font-size:.72rem] [font-weight:700] [margin-bottom:4px] [color:#354024];
  }
  .msg-time {
    @apply [font-size:.68rem] [color:#5A5A5A] [margin-top:4px];
  }
  .chat-form {
    @apply [padding:14px] [border-top:1px_solid_#CFBB99] [background:#fff] [display:flex] [gap:8px];
  }
  .chat-form .input {
    @apply [border-radius:12px];
  }
  .sendbtn {
    @apply [width:46px] [flex:none] [border:0] [border-radius:12px] [background:#889063] [color:#fff] [cursor:pointer] [display:flex] [align-items:center] [justify-content:center];
  }
  .sendbtn:hover {
    @apply [background:#767E54];
  }
  .sendbtn svg.ic {
    @apply [width:18px] [height:18px];
  }
  .chat-empty {
    @apply [text-align:center] [color:#5A5A5A] [padding:40px_0];
  }
  .chat-empty svg.ic {
    @apply [width:36px] [height:36px] [margin-bottom:8px];
  }
  .chat-fab-zone {
    @apply [position:fixed] [bottom:24px] [right:24px] [z-index:100];
  }
  .chat-fab {
    @apply [width:56px] [height:56px] [border:0] [border-radius:50%] [background:#889063] [color:#fff] [cursor:pointer] [display:flex] [align-items:center] [justify-content:center] [box-shadow:0_4px_16px_rgba(0,0,0,.15)];
  }
  .chat-fab:hover {
    @apply [background:#767E54];
  }
  .chat-fab svg.ic {
    @apply [width:24px] [height:24px];
  }
  .chat-win {
    @apply [position:absolute] [bottom:68px] [right:0] [width:360px] [max-width:calc(100vw_-_48px)] [background:#fff] [border:1px_solid_#CFBB99] [border-radius:16px] [box-shadow:0_8px_24px_rgba(0,0,0,0.14)] [overflow:hidden] [display:flex] [flex-direction:column];
  }
  .chat-win-head {
    @apply [background:#889063] [color:#fff] [padding:12px_16px] [display:flex] [justify-content:space-between] [align-items:center];
  }
  .chat-win-head h3 {
    @apply [font-size:1rem];
  }
  .chat-x {
    @apply [background:transparent] [border:0] [color:#fff] [font-size:1.4rem] [cursor:pointer] [line-height:1];
  }
  .chat-win-log {
    @apply [padding:14px] [background:#F9F7F2] [height:280px] [overflow-y:auto];
  }
  .chat-win-form {
    @apply [display:flex] [gap:8px] [padding:10px] [border-top:1px_solid_#CFBB99];
  }
  .chat-win-form .input {
    @apply [min-height:40px] [border-radius:12px];
  }
  .foot {
    @apply [background:#354024] [color:#E5D7C4] [border-top:none] [padding:48px_0_24px] [margin-top:40px];
  }
  .foot-grid {
    @apply [display:grid] [grid-template-columns:1.5fr_1fr_1fr_1fr] [gap:32px] [margin-bottom:32px];
  }
  .foot h4 {
    @apply [color:#fff] [font-size:.85rem] [text-transform:uppercase] [letter-spacing:.06em] [margin-bottom:16px] [padding-bottom:8px] [border-bottom:1px_solid_rgba(136,144,99,.3)];
  }
  .foot p,.foot li {
    @apply [font-size:.8rem] [color:#CFBB99] [line-height:1.7];
  }
  .foot ul {
    @apply [list-style:none];
  }
  .foot a {
    @apply [color:#CFBB99] [text-decoration:none];
  }
  .foot a:hover {
    @apply [color:#fff];
  }
  .foot-brand {
    @apply [display:flex] [align-items:center] [gap:8px] [margin-bottom:12px];
  }
  .foot-brand svg.ic {
    @apply [color:#889063];
  }
  .foot-brand strong {
    @apply [color:#fff] [font-size:1.1rem] [letter-spacing:.04em];
  }
  .foot-copy {
    @apply [border-top:1px_solid_rgba(136,144,99,.2)] [padding-top:16px] [font-size:.75rem] [color:#CFBB9990] [text-align:center];
  }
  .foot-social {
    @apply [display:flex] [gap:10px] [margin-top:12px];
  }
  .foot-social a {
    @apply [width:36px] [height:36px] [border-radius:8px] [background:rgba(255,255,255,.08)] [display:flex] [align-items:center] [justify-content:center] [color:#CFBB99] [transition:background_200ms];
  }
  .foot-social a:hover {
    @apply [background:#889063] [color:#fff];
  }
  .tlink {
    @apply [color:#889063] [font-weight:600] [text-decoration:none];
  }
  .tlink:hover {
    @apply [text-decoration:underline];
  }
}

@media(max-width:960px) {
  .feat3,.svc3 {
    @apply [grid-template-columns:1fr_1fr];
  }
  .book2,.mygrid {
    @apply [grid-template-columns:1fr];
  }
  .sum {
    @apply [position:static];
  }
  .site-links {
    @apply [display:none];
  }
  .menu-toggle {
    @apply [display:inline-flex];
  }
  .hero h1,.hero2 h1 {
    @apply [font-size:2.4rem];
  }
  .hero2,.about2 {
    @apply [grid-template-columns:1fr];
  }
  .loc-grid {
    @apply [grid-template-columns:1fr];
  }
  .foot-grid {
    @apply [grid-template-columns:1fr_1fr];
  }
}

@media(max-width:600px) {
  .feat3,.svc3,.pick-grid,.kv2,.about-feat {
    @apply [grid-template-columns:1fr];
  }
  .hero {
    @apply [padding:52px_0_40px];
  }
  .hero h1,.hero2 h1 {
    @apply [font-size:2rem];
  }
  .hero2 {
    @apply [gap:24px] [padding:52px_0_40px];
  }
  .cal-card {
    @apply [padding:16px_10px];
  }
  .cal-day {
    @apply [width:32px] [height:32px] [font-size:0.82rem];
  }
  .cal-weekday {
    @apply [font-size:0.75rem];
  }
  .slots-grid-5col {
    @apply [gap:6px];
  }
  .slot-btn {
    @apply [height:38px] [font-size:0.75rem];
  }
  .about2 {
    @apply [gap:24px];
  }
  .about-img-ph:nth-child(2) {
    @apply [margin-top:0];
  }
  .foot-grid {
    @apply [grid-template-columns:1fr];
  }
  .section {
    @apply [padding:40px_0];
  }
  .book-main,.auth-panel {
    @apply [padding:22px];
  }
  .cta-band {
    @apply [padding:36px_20px];
  }
  .chat-win {
    @apply [right:-8px];
  }
  .chat-fab-zone {
    @apply [bottom:16px] [right:16px];
  }
}

@theme {
  --animate-scroll-left: scroll-left 30s linear infinite;
  @keyframes scroll-left {
    0% {
      /* Tailwind keyframe utility */
      transform: translateX(0);
    }
    100% {
      /* Tailwind keyframe utility */
      transform: translateX(-50%);
    }
  }
}
</style>
