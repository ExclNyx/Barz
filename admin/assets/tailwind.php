<?php
?>
<style type="text/tailwindcss">
@import "tailwindcss";

@layer base, components;

@layer base {
  html {
    @apply [font-size:16px];
  }
  body {
    @apply [font-family:-apple-system,BlinkMacSystemFont,"Inter","Segoe_UI",Roboto,"Helvetica_Neue",Arial,sans-serif] [background:#F9F7F2] [color:#354024] [line-height:1.55] [-webkit-font-smoothing:antialiased] [overflow-x:hidden];
  }
  a {
    @apply [color:#889063] [text-decoration:none];
  }
  svg.ic {
    @apply [width:18px] [height:18px] [flex:none] [fill:none] [stroke:currentColor] [stroke-width:2] [stroke-linecap:round] [stroke-linejoin:round];
  }
}

@layer components {
  *, *::before, *::after {
    @apply [box-sizing:border-box] [margin:0] [padding:0];
  }
  h1, h2, h3, h4 {
    @apply [font-family:-apple-system,BlinkMacSystemFont,"Inter","Segoe_UI",Roboto,"Helvetica_Neue",Arial,sans-serif] [color:#354024] [font-weight:700] [letter-spacing:-0.015em];
  }
  .shell {
    @apply [display:flex] [min-height:100vh] [background:#F9F7F2];
  }
  .side {
    @apply [position:fixed] [top:0] [left:0] [width:260px] [height:100vh] [background:#2D3A1F] [border-right:1px_solid_rgba(136,_144,_99,_0.15)] [z-index:40] [display:flex] [flex-direction:column] [transition:transform_200ms_cubic-bezier(0.16,_1,_0.3,_1)];
  }
  .side-head {
    @apply [padding:22px_20px] [border-bottom:1px_solid_rgba(136,_144,_99,_0.15)];
  }
  .side-brand {
    @apply [display:flex] [align-items:center] [gap:12px];
  }
  .side-brand-icon {
    @apply [width:38px] [height:38px] [border-radius:10px] [background:rgba(136,144,99,0.12)] [color:#889063] [display:flex] [align-items:center] [justify-content:center] [flex:none];
  }
  .side-brand-icon.super {
    @apply [background:rgba(201,_168,_118,_0.15)] [color:#C9A876];
  }
  .side-brand-icon svg.ic {
    @apply [width:20px] [height:20px];
  }
  .side-brand h2 {
    @apply [color:#F5F0E8] [font-size:1.05rem] [font-weight:800] [letter-spacing:0.05em] [line-height:1.2];
  }
  .side-badge {
    @apply [display:inline-block] [font-size:0.65rem] [font-weight:700] [text-transform:uppercase] [letter-spacing:0.06em] [padding:2px_7px] [border-radius:4px] [background:rgba(136,144,99,0.12)] [color:#889063] [margin-top:2px];
  }
  .side-badge.super {
    @apply [background:rgba(201,_168,_118,_0.2)] [color:#C9A876];
  }
  .side nav {
    @apply [padding:16px_12px] [flex:1] [overflow-y:auto] [display:flex] [flex-direction:column] [gap:4px];
  }
  .side nav a {
    @apply [display:flex] [align-items:center] [gap:12px] [padding:10px_14px] [border-radius:8px] [color:#A8B89A] [text-decoration:none] [font-size:0.88rem] [font-weight:500] [transition:all_140ms_ease];
  }
  .side nav a:hover {
    @apply [background:rgba(136,_144,_99,_0.1)] [color:#F5F0E8] [transform:translateX(2px)];
  }
  .side nav a.on {
    @apply [background:#889063] [color:#fff] [font-weight:600] [box-shadow:0_2px_8px_rgba(136,_144,_99,_0.3)];
  }
  .side-foot {
    @apply [padding:16px_14px] [border-top:1px_solid_rgba(136,_144,_99,_0.15)] [background:rgba(0,0,0,0.15)];
  }
  .user-pill-side {
    @apply [display:flex] [align-items:center] [gap:10px] [padding:6px_8px] [margin-bottom:12px] [background:rgba(255,255,255,0.04)] [border-radius:8px] [border:1px_solid_rgba(255,255,255,0.06)];
  }
  .user-ava-side {
    @apply [width:32px] [height:32px] [border-radius:8px] [background:#889063] [color:#fff] [font-weight:700] [font-size:0.82rem] [display:flex] [align-items:center] [justify-content:center] [flex:none];
  }
  .user-ava-side.super {
    @apply [background:#C9A876];
  }
  .user-meta-side {
    @apply [display:flex] [flex-direction:column] [overflow:hidden];
  }
  .user-name-side {
    @apply [font-size:0.82rem] [font-weight:600] [color:#F5F0E8] [white-space:nowrap] [overflow:hidden] [text-overflow:ellipsis];
  }
  .user-role-side {
    @apply [font-size:0.7rem] [color:#A8B89A];
  }
  .side-foot-links {
    @apply [display:flex] [flex-direction:column] [gap:2px];
  }
  .side-foot-links a {
    @apply [display:flex] [align-items:center] [gap:10px] [padding:8px_10px] [border-radius:6px] [color:#A8B89A] [font-size:0.82rem] [font-weight:500] [transition:all_140ms_ease];
  }
  .side-foot-links a:hover {
    @apply [background:rgba(136,_144,_99,_0.1)] [color:#F5F0E8];
  }
  .side-foot-links a.side-link-danger:hover {
    @apply [background:rgba(217,_127,_110,_0.15)] [color:#D97F6E];
  }
  .veil {
    @apply [display:none] [position:fixed] [inset:0] [background:rgba(45,_58,_31,_0.6)] [backdrop-filter:blur(2px)] [z-index:39];
  }
  .veil.show {
    @apply [display:block];
  }
  .burger {
    @apply [display:none] [position:fixed] [top:14px] [left:14px] [z-index:50] [background:#2D3A1F] [color:#F5F0E8] [border:1px_solid_rgba(136,_144,_99,_0.2)] [border-radius:8px] [padding:10px] [cursor:pointer] [box-shadow:0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)];
  }
  .burger svg.ic {
    @apply [width:20px] [height:20px];
  }
  .main {
    @apply [margin-left:260px] [flex:1] [min-width:0] [min-height:100vh];
  }
  .page {
    @apply [padding:32px_36px] [max-width:1360px] [margin:0_auto];
  }
  .page-top {
    @apply [display:flex] [justify-content:space-between] [align-items:flex-start] [gap:16px] [flex-wrap:wrap] [margin-bottom:28px] [padding-bottom:20px] [border-bottom:1px_solid_#CFBB99];
  }
  .page-top h1 {
    @apply [font-size:1.65rem] [font-weight:800] [color:#354024] [letter-spacing:-0.02em];
  }
  .who {
    @apply [font-size:0.82rem] [font-weight:500] [color:#6B7A5C] [margin-top:4px];
  }
  .stat4 {
    @apply [display:grid] [grid-template-columns:repeat(auto-fit,_minmax(240px,_1fr))] [gap:20px] [margin-bottom:28px];
  }
  .stat {
    @apply [background:#FFF8F0] [border:1px_solid_#CFBB99] [border-radius:16px] [padding:22px_20px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] [display:flex] [justify-content:space-between] [align-items:center] [transition:all_150ms_ease];
  }
  .stat:hover {
    @apply [transform:translateY(-2px)] [border-color:#E5D7C4] [box-shadow:0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)];
  }
  .stat small {
    @apply [display:block] [font-size:0.74rem] [font-weight:600] [text-transform:uppercase] [letter-spacing:0.05em] [color:#6B7A5C] [margin-bottom:6px];
  }
  .stat b {
    @apply [font-size:1.65rem] [font-weight:800] [color:#354024] [line-height:1.2] [letter-spacing:-0.02em];
  }
  .stat-ic {
    @apply [width:48px] [height:48px] [border-radius:12px] [display:flex] [align-items:center] [justify-content:center] [flex:none];
  }
  .stat-ic svg.ic {
    @apply [width:22px] [height:22px];
  }
  .stat-ic.g {
    @apply [background:#D4E8D2] [color:#4A7C3F];
  }
  .stat-ic.t {
    @apply [background:#DCE8F0] [color:#3A5A7A];
  }
  .stat-ic.o {
    @apply [background:#F5E8C8] [color:#7A5C1A];
  }
  .stat-ic.r {
    @apply [background:#F5D6D1] [color:#8B3A2E];
  }
  .page-stats {
    @apply [display:flex] [gap:8px] [flex-wrap:wrap];
  }
  .stat-pill {
    @apply [display:inline-flex] [align-items:center] [gap:6px] [padding:6px_14px] [background:#FFF8F0] [border:1px_solid_#CFBB99] [border-radius:999px] [font-size:0.8rem] [font-weight:500] [color:#6B7A5C] [box-shadow:0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)];
  }
  .stat-pill strong {
    @apply [color:#354024] [font-weight:700];
  }
  .stat-pill.pending {
    @apply [background:#FDF3E0] [border-color:#E8C58A] [color:#8B5A1A];
  }
  .stat-pill.confirmed {
    @apply [background:#E0F0E0] [border-color:#A8D8A8] [color:#2D5A2D];
  }
  .stat-pill.completed {
    @apply [background:#E0E8F0] [border-color:#A8C8E8] [color:#2D4A6B];
  }
  .stat-pill.cancelled {
    @apply [background:#F5E0E0] [border-color:#E8A8A8] [color:#7A2D2D];
  }
  .act2 {
    @apply [display:grid] [grid-template-columns:repeat(auto-fit,_minmax(240px,_1fr))] [gap:16px] [margin-bottom:28px];
  }
  .act {
    @apply [background:#FFF8F0] [border:1px_solid_#CFBB99] [border-radius:12px] [padding:18px_20px] [text-decoration:none] [display:flex] [align-items:center] [gap:16px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] [transition:all_150ms_ease];
  }
  .act:hover {
    @apply [border-color:#889063] [background:#F9F7F2] [transform:translateY(-2px)] [box-shadow:0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)];
  }
  .act-ic {
    @apply [width:44px] [height:44px] [border-radius:10px] [background:#F1E8DB] [color:#354024] [display:flex] [align-items:center] [justify-content:center] [flex:none] [transition:all_150ms_ease];
  }
  .act:hover .act-ic {
    @apply [background:#889063] [color:#fff];
  }
  .act-ic svg.ic {
    @apply [width:20px] [height:20px];
  }
  .act b {
    @apply [display:block] [font-size:0.95rem] [font-weight:700] [color:#354024] [margin-bottom:2px];
  }
  .act small {
    @apply [font-size:0.8rem] [color:#6B7A5C];
  }
  .card {
    @apply [background:#FFF8F0] [border:1px_solid_#CFBB99] [border-radius:16px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] [margin-bottom:24px] [overflow:hidden];
  }
  .card-h {
    @apply [padding:18px_24px] [border-bottom:1px_solid_#E5D7C4] [display:flex] [justify-content:space-between] [align-items:center] [gap:12px] [flex-wrap:wrap];
  }
  .card-h h3 {
    @apply [font-size:1.1rem] [font-weight:700] [color:#354024] [margin:0];
  }
  .card-b {
    @apply [padding:24px];
  }
  .filter-card {
    @apply [background:#FFF8F0] [border:1px_solid_#CFBB99] [border-radius:16px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] [margin-bottom:20px];
  }
  .filter-form {
    @apply [display:flex] [gap:16px] [align-items:flex-end] [flex-wrap:wrap] [padding:20px];
  }
  .filter-group {
    @apply [display:flex] [flex-direction:column] [gap:6px] [flex:1] [min-width:160px];
  }
  .filter-label {
    @apply [font-size:0.72rem] [font-weight:700] [text-transform:uppercase] [letter-spacing:0.05em] [color:#6B7A5C];
  }
  .filter-select, .filter-input {
    @apply [padding:10px_14px] [border:1px_solid_#CFBB99] [border-radius:8px] [font-size:0.88rem] [font-family:-apple-system,BlinkMacSystemFont,"Inter","Segoe_UI",Roboto,"Helvetica_Neue",Arial,sans-serif] [background:#FFF8F0] [color:#354024] [transition:all_140ms_ease];
  }
  .filter-select:focus, .filter-input:focus {
    @apply [outline:none] [border-color:#889063] [box-shadow:0_0_0_3px_rgba(136,144,99,0.12)];
  }
  .filter-actions {
    @apply [display:flex] [align-items:flex-end];
  }
  .btn-sm {
    @apply [padding:8px_16px] [font-size:0.82rem];
  }
  .twrap, .table-wrapper {
    @apply [overflow-x:auto] [width:100%] [border:1px_solid_#CFBB99] [border-radius:10px] [background:#FFF8F0];
  }
  table {
    @apply [width:100%] [border-collapse:separate] [border-spacing:0] [text-align:left];
  }
  thead th {
    @apply [background:#F9F7F2] [color:#6B7A5C] [font-size:0.72rem] [font-weight:700] [text-transform:uppercase] [letter-spacing:0.06em] [padding:14px_18px] [border-bottom:1px_solid_#CFBB99] [white-space:nowrap];
  }
  tbody td {
    @apply [padding:14px_18px] [font-size:0.88rem] [color:#354024] [border-bottom:1px_solid_#E5D7C4] [vertical-align:middle];
  }
  tbody tr {
    @apply [transition:background_120ms_ease];
  }
  tbody tr:hover {
    @apply [background:#F9F7F2];
  }
  tbody tr:last-child td {
    @apply [border-bottom:none];
  }
  .customer-col {
    @apply [min-width:200px];
  }
  .customer-info {
    @apply [display:flex] [flex-direction:column] [gap:2px];
  }
  .customer-info strong {
    @apply [font-weight:600] [color:#354024];
  }
  .customer-info span {
    @apply [font-size:0.78rem] [color:#6B7A5C];
  }
  .price-col {
    @apply [font-weight:700] [color:#889063] [white-space:nowrap];
  }
  .actions-col {
    @apply [min-width:160px] [white-space:nowrap];
  }
  .action-group {
    @apply [display:flex] [gap:6px] [margin-bottom:6px];
  }
  .action-btn {
    @apply [display:inline-flex] [align-items:center] [justify-content:center] [width:32px] [height:32px] [border:1px_solid_#CFBB99] [background:#FFF8F0] [border-radius:8px] [color:#6B7A5C] [cursor:pointer] [transition:all_140ms_ease];
  }
  .action-btn:hover {
    @apply [background:#889063] [border-color:#889063] [color:#fff] [transform:translateY(-1px)];
  }
  .quick-status {
    @apply [display:flex] [gap:4px] [flex-wrap:wrap];
  }
  .quick-status-form {
    @apply [margin:0];
  }
  .quick-status-btn {
    @apply [display:inline-flex] [align-items:center] [justify-content:center] [padding:4px_8px] [border-radius:6px] [font-size:0.68rem] [font-weight:600] [border:1px_solid_transparent] [cursor:pointer] [transition:all_140ms_ease] [font-family:-apple-system,BlinkMacSystemFont,"Inter","Segoe_UI",Roboto,"Helvetica_Neue",Arial,sans-serif] [white-space:nowrap];
  }
  .quick-status-btn:disabled {
    @apply [opacity:0.45] [cursor:not-allowed];
  }
  .quick-status-btn:not(:disabled):hover {
    @apply [transform:translateY(-1px)];
  }
  .quick-status-btn.active {
    @apply [box-shadow:0_0_0_2px_currentColor] [font-weight:700];
  }
  .quick-status-btn.tag-pending {
    @apply [background:#FDF3E0] [color:#8B5A1A] [border-color:#E8C58A];
  }
  .quick-status-btn.tag-pending.active {
    @apply [background:#C9A876] [color:#fff];
  }
  .quick-status-btn.tag-confirmed {
    @apply [background:#E0F0E0] [color:#2D5A2D] [border-color:#A8D8A8];
  }
  .quick-status-btn.tag-confirmed.active {
    @apply [background:#6FA86F] [color:#fff];
  }
  .quick-status-btn.tag-completed {
    @apply [background:#E0E8F0] [color:#2D4A6B] [border-color:#A8C8E8];
  }
  .quick-status-btn.tag-completed.active {
    @apply [background:#4A6FA5] [color:#fff];
  }
  .quick-status-btn.tag-cancelled {
    @apply [background:#F5E0E0] [color:#7A2D2D] [border-color:#E8A8A8];
  }
  .quick-status-btn.tag-cancelled.active {
    @apply [background:#D97F6E] [color:#fff];
  }
  .tag {
    @apply [display:inline-flex] [align-items:center] [gap:6px] [padding:4px_10px] [border-radius:999px] [font-size:0.74rem] [font-weight:600] [text-transform:capitalize] [letter-spacing:0.01em];
  }
  .tag::before {
    @apply [content:""] [width:6px] [height:6px] [border-radius:50%] [background:currentColor];
  }
  .tag-pending {
    @apply [background:#FDF3E0] [color:#8B5A1A] [border:1px_solid_#E8C58A];
  }
  .tag-confirmed {
    @apply [background:#E0F0E0] [color:#2D5A2D] [border:1px_solid_#A8D8A8];
  }
  .tag-completed {
    @apply [background:#E0E8F0] [color:#2D4A6B] [border:1px_solid_#A8C8E8];
  }
  .tag-cancelled {
    @apply [background:#F5E0E0] [color:#7A2D2D] [border:1px_solid_#E8A8A8];
  }
  .tag-no_show {
    @apply [background:#F1E8DB] [color:#6B7A5C] [border:1px_solid_#CFBB99];
  }
  .btn {
    @apply [display:inline-flex] [align-items:center] [justify-content:center] [gap:8px] [padding:10px_18px] [border-radius:16px] [font-size:0.86rem] [font-weight:600] [text-decoration:none] [cursor:pointer] [border:1px_solid_transparent] [transition:all_140ms_ease] [font-family:-apple-system,BlinkMacSystemFont,"Inter","Segoe_UI",Roboto,"Helvetica_Neue",Arial,sans-serif];
  }
  .btn svg.ic {
    @apply [width:16px] [height:16px];
  }
  .btn-p {
    @apply [background:#889063] [color:#fff] [border-color:#889063];
  }
  .btn-p:hover {
    @apply [background:#767E54] [border-color:#767E54] [transform:translateY(-1px)] [box-shadow:0_4px_10px_rgba(136,_144,_99,_0.3)];
  }
  .btn-o {
    @apply [background:#FFF8F0] [color:#354024] [border-color:#CFBB99];
  }
  .btn-o:hover {
    @apply [background:#F9F7F2] [color:#354024] [border-color:#E5D7C4];
  }
  .btn-d {
    @apply [background:#F5E0E0] [color:#D97F6E] [border-color:#E8A8A8];
  }
  .btn-d:hover {
    @apply [background:#D97F6E] [color:#fff] [border-color:#D97F6E];
  }
  .btn-g {
    @apply [background:#E0F0E0] [color:#6FA86F] [border-color:#A8D8A8];
  }
  .btn-g:hover {
    @apply [background:#6FA86F] [color:#fff] [border-color:#6FA86F];
  }
  .btn-s {
    @apply [padding:6px_12px] [font-size:0.78rem] [border-radius:8px];
  }
  .notice {
    @apply [border-radius:10px] [padding:14px_18px] [font-size:0.88rem] [margin-bottom:20px] [display:flex] [align-items:center] [gap:10px] [border:1px_solid_transparent];
  }
  .notice svg.ic {
    @apply [width:18px] [height:18px] [flex:none];
  }
  .notice-ok {
    @apply [background:#E0F0E0] [color:#2D5A2D] [border-color:#A8D8A8];
  }
  .notice-error {
    @apply [background:#F5E0E0] [color:#7A2D2D] [border-color:#E8A8A8];
  }
  .notice-info {
    @apply [background:#E0E8F0] [color:#2D4A6B] [border-color:#A8C8E8];
  }
  .field {
    @apply [margin-bottom:16px];
  }
  .label {
    @apply [display:block] [font-size:0.82rem] [font-weight:600] [color:#354024] [margin-bottom:6px];
  }
  .input, .select, .area {
    @apply [width:100%] [padding:10px_14px] [border:1px_solid_#CFBB99] [border-radius:8px] [font-size:0.9rem] [font-family:-apple-system,BlinkMacSystemFont,"Inter","Segoe_UI",Roboto,"Helvetica_Neue",Arial,sans-serif] [background:#FFF8F0] [color:#354024] [transition:all_140ms_ease];
  }
  .input:focus, .select:focus, .area:focus {
    @apply [outline:none] [border-color:#889063] [box-shadow:0_0_0_3px_rgba(136,144,99,0.12)];
  }
  .input::placeholder, .area::placeholder {
    @apply [color:#A8B89A];
  }
  dialog.modal {
    @apply [border:1px_solid_#CFBB99] [border-radius:16px] [padding:0] [max-width:500px] [width:calc(100vw_-_40px)] [box-shadow:0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)] [background:#FFF8F0] [color:#354024] [margin:auto];
  }
  dialog.modal::backdrop {
    @apply [background:rgba(45,_58,_31,_0.6)] [backdrop-filter:blur(4px)];
  }
  .modal-h {
    @apply [padding:18px_24px] [border-bottom:1px_solid_#E5D7C4] [display:flex] [justify-content:space-between] [align-items:center] [gap:12px];
  }
  .modal-h h3 {
    @apply [font-size:1.1rem] [font-weight:700] [color:#354024] [margin:0];
  }
  .modal-x {
    @apply [background:transparent] [border:0] [font-size:1.4rem] [cursor:pointer] [color:#6B7A5C] [line-height:1] [padding:4px] [border-radius:6px] [transition:all_140ms_ease];
  }
  .modal-x:hover {
    @apply [color:#354024] [background:#F1E8DB];
  }
  .modal-b {
    @apply [padding:24px];
  }
  .modal-f {
    @apply [padding:16px_24px] [border-top:1px_solid_#E5D7C4] [display:flex] [justify-content:flex-end] [gap:10px] [background:#F9F7F2];
  }
  .kv {
    @apply [display:grid] [grid-template-columns:140px_1fr] [gap:10px_14px] [font-size:0.88rem];
  }
  .kv dt {
    @apply [color:#6B7A5C] [font-weight:500];
  }
  .kv dd {
    @apply [font-weight:600] [color:#354024];
  }
  .chatgrid {
    @apply [display:grid] [grid-template-columns:repeat(auto-fill,_minmax(300px,_1fr))] [gap:18px];
  }
  .chatcard {
    @apply [background:#FFF8F0] [border:1px_solid_#CFBB99] [border-radius:16px] [padding:20px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] [display:flex] [flex-direction:column] [gap:12px] [transition:all_150ms_ease];
  }
  .chatcard:hover {
    @apply [border-color:#889063] [box-shadow:0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)];
  }
  .chatcard .rowline {
    @apply [display:flex] [justify-content:space-between] [align-items:flex-start] [gap:10px];
  }
  .chatcard h4 {
    @apply [font-size:1rem] [font-weight:700] [color:#354024] [margin-bottom:2px];
  }
  .chatcard small {
    @apply [font-size:0.8rem] [color:#6B7A5C];
  }
  .chatcard .count {
    @apply [display:inline-flex] [align-items:center] [justify-content:center] [padding:2px_8px] [border-radius:999px] [font-size:0.72rem] [font-weight:700] [background:#F5E0E0] [color:#D97F6E];
  }
  .chatcard .meta {
    @apply [display:inline-flex] [align-items:center] [gap:6px] [font-size:0.78rem] [color:#6B7A5C];
  }
  .chat-shell {
    @apply [background:#FFF8F0] [border:1px_solid_#CFBB99] [border-radius:16px] [box-shadow:0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] [display:flex] [flex-direction:column] [overflow:hidden];
  }
  .chat-head {
    @apply [padding:16px_20px] [border-bottom:1px_solid_#CFBB99] [background:#F9F7F2] [display:flex] [align-items:center] [gap:12px];
  }
  .chat-head .avatar {
    @apply [width:40px] [height:40px] [border-radius:10px] [background:#2D3A1F] [color:#fff] [display:flex] [align-items:center] [justify-content:center] [flex:none];
  }
  .chat-head strong {
    @apply [display:block] [font-size:0.95rem] [color:#354024];
  }
  .chat-head small {
    @apply [font-size:0.8rem] [color:#6B7A5C];
  }
  .chat-log {
    @apply [padding:20px] [background:#F9F7F2] [height:450px] [overflow-y:auto];
  }
  .msgcol {
    @apply [display:flex] [flex-direction:column] [gap:14px];
  }
  .msg {
    @apply [max-width:75%] [display:flex] [flex-direction:column];
  }
  .msg.sent {
    @apply [align-self:flex-end] [align-items:flex-end];
  }
  .msg.in {
    @apply [align-self:flex-start] [align-items:flex-start];
  }
  .bubble {
    @apply [padding:10px_16px] [border-radius:14px] [font-size:0.9rem] [line-height:1.45] [box-shadow:0_1px_2px_rgba(0,0,0,0.04)];
  }
  .msg.sent .bubble {
    @apply [background:#889063] [color:#fff] [border-bottom-right-radius:3px];
  }
  .msg.in .bubble {
    @apply [background:#FFF8F0] [border:1px_solid_#CFBB99] [color:#354024] [border-bottom-left-radius:3px];
  }
  .bubble .who {
    @apply [display:block] [font-size:0.72rem] [font-weight:700] [margin-bottom:2px] [color:#889063];
  }
  .msg-time {
    @apply [font-size:0.7rem] [color:#6B7A5C] [margin-top:4px] [padding:0_4px];
  }
  .chat-form {
    @apply [padding:16px_20px] [border-top:1px_solid_#CFBB99] [background:#FFF8F0] [display:flex] [gap:10px];
  }
  .sendbtn {
    @apply [width:44px] [height:44px] [border:0] [border-radius:10px] [background:#889063] [color:#fff] [cursor:pointer] [display:flex] [align-items:center] [justify-content:center] [flex:none] [transition:all_140ms_ease];
  }
  .sendbtn:hover {
    @apply [background:#767E54] [transform:translateY(-1px)];
  }
  .empty-state, .center {
    @apply [text-align:center] [color:#6B7A5C] [padding:36px_20px];
  }
  .empty-state svg.ic {
    @apply [width:48px] [height:48px] [margin-bottom:12px] [opacity:0.5];
  }
  .empty-state h3 {
    @apply [font-size:1.1rem] [margin-bottom:6px] [color:#354024];
  }
  .empty-state p {
    @apply [font-size:0.9rem];
  }
}

@media (max-width: 1200px) {
  .stat4 {
    @apply [grid-template-columns:repeat(2,_1fr)];
  }
}

@media (max-width: 768px) {
  .side {
    @apply [transform:translateX(-100%)];
  }
  .side.show {
    @apply [transform:translateX(0)];
  }
  .main {
    @apply [margin-left:0];
  }
  .burger {
    @apply [display:block];
  }
  .page {
    @apply [padding:20px_16px] [padding-top:68px];
  }
  .page-top {
    @apply [flex-direction:column] [align-items:stretch];
  }
  .stat4 {
    @apply [grid-template-columns:1fr] [gap:12px];
  }
  .act2 {
    @apply [grid-template-columns:1fr];
  }
  .filter-form {
    @apply [flex-direction:column] [align-items:stretch];
  }
  .filter-group {
    @apply [width:100%];
  }
}
</style>
