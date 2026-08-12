@extends('layouts.app')

@push('style')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* Premium Font & Global Style Override */
    .faq-container {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    /* Hero Section Slate Gradient */
    .faq-hero {
        background: linear-gradient(135deg, #101c3d 0%, #1e2d5a 100%);
        border-radius: 20px;
        padding: 3rem 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(16, 28, 61, 0.15);
    }
    
    .faq-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(13, 110, 253, 0.15) 0%, transparent 60%);
        border-radius: 50%;
    }

    /* Custom Search Box */
    .faq-search-wrapper {
        position: relative;
        max-width: 650px;
        margin: 0 auto;
    }

    .faq-search-box {
        height: 58px;
        border-radius: 16px;
        padding-left: 55px;
        padding-right: 120px;
        border: 2px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.95);
        font-size: 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .faq-search-box:focus {
        border-color: #0d6efd;
        background: #ffffff;
        box-shadow: 0 8px 30px rgba(13, 110, 253, 0.2);
        outline: none;
    }

    .faq-search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-size: 1.25rem;
    }

    .faq-search-btn-absolute {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        height: 42px;
        border-radius: 12px;
        padding: 0 24px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Category List Cards */
    .category-card-custom {
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        border-radius: 16px !important;
        background: #ffffff;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .faq-category-item {
        border: none !important;
        margin: 4px 8px;
        border-radius: 10px !important;
        font-weight: 500;
        color: #495057;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 12px 16px;
    }

    .faq-category-item:hover {
        background-color: #f8f9fa;
        color: #0d6efd;
        transform: translateX(4px);
    }

    .faq-category-item.active {
        background-color: #e7f1ff !important;
        color: #0d6efd !important;
        font-weight: 600;
    }

    .faq-category-item .badge {
        font-size: 0.75rem;
        padding: 5px 9px;
        border-radius: 20px;
    }

    /* Premium FAQ Accordion */
    .faq-item-card {
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 16px !important;
        overflow: hidden;
        margin-bottom: 16px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
    }

    .faq-item-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        border-color: rgba(13, 110, 253, 0.2);
    }

    .faq-accordion .accordion-button {
        padding: 20px 24px;
        font-weight: 600;
        color: #212529;
        background-color: #ffffff;
        font-size: 1rem;
        border: none !important;
        box-shadow: none !important;
        transition: all 0.3s ease;
    }

    .faq-accordion .accordion-button:not(.collapsed) {
        color: #0d6efd;
        background-color: #f8fbff;
        border-bottom: 1px solid rgba(13, 110, 253, 0.1) !important;
    }

    .faq-accordion .accordion-body {
        padding: 24px;
        background-color: #fafbfc;
        color: #495057;
        font-size: 0.95rem;
        line-height: 1.6;
        border-top: 1px solid rgba(0, 0, 0, 0.03);
    }

    .category-tag {
        display: inline-block;
        padding: 4px 10px;
        background-color: #f0f4f9;
        color: #495057;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 6px;
        margin-right: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .faq-accordion .accordion-button:not(.collapsed) .category-tag {
        background-color: #e7f1ff;
        color: #0d6efd;
    }

    /* Empty state styling */
    .empty-state-card {
        border-radius: 20px;
        border: 2px dashed #dee2e6;
        padding: 4rem 2rem;
        background: #ffffff;
    }

    /* Chat bubble UI styling */
    .chat-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 20px !important;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.02);
    }
    .chat-bubble-left {
        background-color: #ffffff;
        border-radius: 18px;
        border-top-left-radius: 4px;
        color: #212529;
        border: 1px solid rgba(0, 0, 0, 0.06);
        max-width: 75%;
    }
    .chat-bubble-right {
        background-color: #0d6efd;
        color: #ffffff;
        border-radius: 18px;
        border-top-right-radius: 4px;
        max-width: 75%;
    }
</style>
@endpush

@section('content')
<div class="box mx-2 py-4 faq-container">
    <div class="container-fluid">
        
        {{-- Hero Header --}}
        <div class="faq-hero mb-4 text-white">
            <div class="row align-items-center">
                <div class="col-lg-8 text-center text-lg-start mb-3 mb-lg-0">
                    <span class="badge bg-primary px-3 py-2 mb-2 text-uppercase fw-bold rounded-pill" style="letter-spacing: 1px;">Pusat Bantuan & Panduan</span>
                    <h1 class="fw-bold mb-2 display-6" style="font-size: 2.2rem;">Bagaimana kami bisa membantu Anda?</h1>
                    <p class="text-white-50 mb-0">Tanyakan kendala teknis atau cari solusi dokumen Mindik secara instan.</p>
                </div>
                <div class="col-lg-4 text-center text-lg-end">
                    <a href="{{ asset('pdf-manual-book/Tutorial_ICELL_PDF.pdf') }}" target="_blank" class="btn btn-danger btn-lg px-4 py-3 fw-bold rounded-pill shadow-lg" style="font-size: 0.95rem;">
                        <i class="bi bi-file-earmark-pdf-fill mr-2"></i> Unduh Panduan (PDF)
                    </a>
                </div>
            </div>

            {{-- Search Bar FAQ --}}
            <div class="row mt-4">
                <div class="col-lg-8">
                    <form action="{{ route('tutorial.index') }}" method="GET">
                        @if(request('kategori'))
                            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                        @endif
                        <div class="input-group shadow-lg" style="border-radius: 30px; overflow: hidden; background: #ffffff; border: 1px solid rgba(0,0,0,0.15); height: 50px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-transparent border-0 pl-3 pr-2 text-muted h-100 d-flex align-items-center">
                                    <i class="bi bi-search" style="font-size: 1.1rem;"></i>
                                </span>
                            </div>
                            <input type="text" name="search" class="form-control border-0 bg-transparent h-100" 
                                   placeholder="Cari solusi kendala teknis (contoh: lupa password, gagal TTE)..." 
                                   value="{{ request('chat') ? '' : $search }}" style="box-shadow: none !important; font-size: 0.95rem; color: #333333;">
                            <div class="input-group-append">
                                <button class="btn btn-primary px-4 h-100 fw-bold border-0" type="submit" style="font-size: 0.95rem; font-weight: 600; border-radius: 0; display: inline-flex; align-items: center; justify-content: center;">
                                    Cari Solusi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Main Grid --}}
        <div class="row">
            {{-- Category Filter Menu --}}
            <div class="col-lg-3 mb-4">
                <div class="card category-card-custom border-0">
                    <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                        <h5 class="fw-bold mb-0 text-blue-dark">Kategori Pilihan</h5>
                    </div>
                    <div class="list-group list-group-flush py-2">
                        <a href="{{ route('tutorial.index', ['search' => $search]) }}" 
                           class="list-group-item list-group-item-action faq-category-item d-flex justify-content-between align-items-center {{ !request('kategori') ? 'active' : '' }}">
                            <span><i class="bi bi-grid-fill me-2"></i>Semua Kategori</span>
                            <span class="badge bg-secondary rounded-pill">All</span>
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('tutorial.index', ['kategori' => $cat, 'search' => $search]) }}" 
                               class="list-group-item list-group-item-action faq-category-item d-flex justify-content-between align-items-center {{ request('kategori') == $cat ? 'active' : '' }}">
                                <span><i class="bi bi-bookmark-fill me-2"></i>{{ $cat }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Accordion FAQ List --}}
            <div class="col-lg-9">
                @if($faqs->isEmpty())
                    <div class="empty-state-card text-center shadow-sm mb-4">
                        <i class="bi bi-patch-question text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-4 fw-bold text-dark">Solusi Tidak Ditemukan</h4>
                        <p class="text-muted col-md-8 mx-auto">Kami tidak menemukan jawaban untuk kata kunci tersebut. Coba gunakan kata kunci umum lainnya atau kembali ke beranda tutorial.</p>
                        <a href="{{ route('tutorial.index') }}" class="btn btn-primary px-4 mt-2 rounded-pill fw-semibold">
                            Lihat Semua FAQ
                        </a>
                    </div>
                @else
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h4 class="fw-bold mb-0 text-blue-dark">
                                @if(request('kategori'))
                                    Kategori: <span class="text-primary">{{ request('kategori') }}</span>
                                @else
                                    Pertanyaan yang Sering Diajukan (FAQ)
                                @endif
                            </h4>
                            <span class="badge bg-primary px-3 py-2 rounded-pill fw-semibold">Total: {{ $faqs->count() }} Solusi</span>
                        </div>

                        <div class="accordion faq-accordion" id="faqAccordion">
                            @foreach($faqs as $index => $faq)
                                <div class="accordion-item faq-item-card">
                                    <h2 class="accordion-header" id="heading{{ $faq->id }}">
                                        <button class="accordion-button collapsed" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}" 
                                                aria-expanded="false" aria-controls="collapse{{ $faq->id }}">
                                            <span class="category-tag">{{ $faq->kategori }}</span>
                                            {{ $faq->pertanyaan }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse" 
                                         aria-labelledby="heading{{ $faq->id }}" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body" style="white-space: pre-line;">
                                            {!! nl2br(e($faq->jawaban)) !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Floating Chat Widget --}}
                <div class="faq-chat-widget">
                    {{-- Chat Interface BOT-ICELL (Floating Window) --}}
                    <div class="card chat-card border-0 shadow-lg {{ request('chat') || session('faq_chat_history') ? '' : 'd-none' }}" id="chat-section" style="position: fixed; bottom: 100px; right: 30px; z-index: 99999; width: 380px; max-width: 90vw; border-radius: 20px; overflow: hidden;">
                        <div class="card-header text-white d-flex align-items-center justify-content-between py-3 px-4" style="background: linear-gradient(135deg, #101c3d 0%, #1e2d5a 100%); border-top-left-radius: 20px; border-top-right-radius: 20px; position: relative;">
                            <div class="d-flex align-items-center">
                                <div class="position-relative mr-3">
                                    <span class="rounded-circle d-flex align-items-center justify-content-center bg-white text-primary" style="width: 40px; height: 40px; font-size: 1.3rem; shadow: 0 2px 8px rgba(0,0,0,0.15);">
                                        🤖
                                    </span>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-white" style="font-size: 0.95rem; letter-spacing: 0.5px;">BOT-ICELL</h5>
                                    <span class="text-white-50 small" style="font-size: 0.75rem;"><i class="bi bi-circle-fill text-success mr-1" style="font-size: 0.4rem;"></i> Asisten Robot (Online)</span>
                                </div>
                            </div>
                            <div style="position: absolute; top: 50%; right: 20px; transform: translateY(-50%); display: flex; align-items: center;">
                                <a href="{{ route('tutorial.index', ['reset_chat' => 1]) }}#chat-section" class="text-white-50 p-0" title="Bersihkan Chat" style="font-size: 1.2rem; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-3 bg-light" style="height: 350px; overflow-y: auto;">
                            <div class="d-flex flex-column" id="chat-messages-container">
                                
                                {{-- Welcome Message --}}
                                <div class="d-flex align-items-start mb-3">
                                    <span class="rounded-circle d-flex align-items-center justify-content-center bg-white border text-primary mr-2" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.95rem;">
                                        🤖
                                    </span>
                                    <div class="p-3 chat-bubble-left shadow-sm" style="font-size: 0.85rem; border-radius: 12px; border-top-left-radius: 4px;">
                                        <p class="mb-0 fw-semibold text-primary" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">BOT-ICELL</p>
                                        <p class="mb-0 mt-1">Sistem Robot Asisten ICELL aktif. Silakan masukkan pertanyaan Anda pada kolom di bawah.</p>
                                    </div>
                                </div>

                                {{-- Session History Loop --}}
                                @foreach(session('faq_chat_history', []) as $message)
                                    @if($message['sender'] == 'user')
                                        {{-- User Search Bubble --}}
                                        <div class="d-flex align-items-start mb-3 justify-content-end">
                                            <div class="p-3 chat-bubble-right shadow-sm mr-2" style="font-size: 0.85rem; border-radius: 12px; border-top-right-radius: 4px;">
                                                <p class="mb-0 fw-semibold text-white-50" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Anda</p>
                                                <p class="mb-0 mt-1">{{ $message['text'] }}</p>
                                                <small class="text-white-50 d-block text-end mt-1" style="font-size: 0.65rem;">{{ $message['time'] }}</small>
                                            </div>
                                            <span class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold shadow-sm" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.95rem;">
                                                {{ substr(Auth::user()->first_name ?? 'P', 0, 1) }}
                                            </span>
                                        </div>
                                    @else
                                        {{-- AI Smart Response Bubble --}}
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="rounded-circle d-flex align-items-center justify-content-center bg-white border text-primary mr-2" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.95rem;">
                                                🤖
                                            </span>
                                            <div class="p-3 chat-bubble-left shadow-sm" style="font-size: 0.85rem; border-radius: 12px; border-top-left-radius: 4px;">
                                                <p class="mb-0 fw-semibold text-primary" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">BOT-ICELL</p>
                                                <div class="mb-0 mt-1 text-dark" style="white-space: pre-line; line-height: 1.5; max-width: 250px;">
                                                    {!! nl2br(
                                                        str_contains($message['text'], '[') 
                                                        ? preg_replace('/\[([^\]]+)\]\((https?:\/\/[^\s\)]+)\)/', '<a href="$2" target="_blank" class="text-primary fw-bold text-decoration-underline">$1</a>', e($message['text']))
                                                        : preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank" class="text-primary fw-bold text-decoration-underline">$1</a>', e($message['text']))
                                                    ) !!}
                                                </div>
                                                <small class="text-muted d-block mt-1" style="font-size: 0.65rem;">{{ $message['time'] }}</small>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                            </div>
                        </div>
                        {{-- Chat Input Footer --}}
                        <div class="card-footer bg-white border-top p-3" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                            <form action="{{ route('tutorial.index') }}#chat-section" method="GET" id="chat-input-form">
                                <input type="hidden" name="chat" value="1">
                                @if(request('kategori'))
                                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                                @endif
                                <div class="input-group" style="border-radius: 30px; overflow: hidden; border: 1px solid #ced4da; background-color: #f8f9fa; padding: 4px;">
                                    <input type="text" name="search" id="chat-search-input" class="form-control border-0 bg-transparent px-3 py-1" 
                                           placeholder="Ketik pertanyaan Anda..." 
                                           value="" style="box-shadow: none !important; font-size: 0.85rem; height: 32px;" required autocomplete="off">
                                    <button class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" id="chat-send-btn" type="submit" style="width: 32px; height: 32px; padding: 0;">
                                        <i class="bi bi-send-fill text-white" style="font-size: 0.8rem;"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Floating Toggle Button --}}
                    <button type="button" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center animate__animated animate__bounceIn" id="toggle-chat-widget" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 99999; border: 2px white solid; background: linear-gradient(135deg, #101c3d 0%, #1e2d5a 100%); transition: all 0.3s ease;">
                        <span style="font-size: 1.8rem; line-height: 1;" id="toggle-icon">🤖</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatBody = document.querySelector('.chat-card .card-body');
        
        // Auto-scroll chat window ke baris paling bawah saat load awal
        if (chatBody) {
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        // Tangkap event submit untuk menyisipkan chat instan sebelum reload
        const chatForm = document.getElementById('chat-input-form');
        if (chatForm) {
            chatForm.addEventListener('submit', function(e) {
                const inputEl = document.getElementById('chat-search-input');
                const sendBtn = document.getElementById('chat-send-btn');
                const messagesContainer = document.getElementById('chat-messages-container');
                
                const text = inputEl.value.trim();
                if (!text) return;

                // Escape HTML helper
                const escapeHtml = (str) => {
                    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
                };

                const userInitial = "{{ substr(Auth::user()->first_name ?? 'P', 0, 1) }}";

                // 1. Masukkan bubble chat user instan (ukuran disesuaikan widget melayang)
                const userBubble = `
                    <div class="d-flex align-items-start mb-3 justify-content-end animate__animated animate__fadeIn">
                        <div class="p-3 chat-bubble-right shadow-sm mr-2" style="font-size: 0.85rem; border-radius: 12px; border-top-right-radius: 4px;">
                            <p class="mb-0 fw-semibold text-white-50" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Anda</p>
                            <p class="mb-0 mt-1">${escapeHtml(text)}</p>
                            <small class="text-white-50 d-block text-end mt-1" style="font-size: 0.65rem;">Sekarang</small>
                        </div>
                        <span class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold shadow-sm" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.95rem;">
                            ${userInitial}
                        </span>
                    </div>
                `;
                messagesContainer.insertAdjacentHTML('beforeend', userBubble);

                // 2. Masukkan bubble loading BOT-ICELL instan
                const botLoading = `
                    <div class="d-flex align-items-start mb-3 animate__animated animate__fadeIn" id="bot-typing-indicator">
                        <span class="rounded-circle d-flex align-items-center justify-content-center bg-white border text-primary mr-2" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.95rem;">
                            🤖
                        </span>
                        <div class="p-3 chat-bubble-left shadow-sm" style="font-size: 0.85rem; border-radius: 12px; border-top-left-radius: 4px;">
                            <p class="mb-0 fw-semibold text-primary" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">BOT-ICELL</p>
                            <div class="mb-0 mt-1 d-flex align-items-center">
                                <span class="spinner-grow spinner-grow-sm text-primary mr-2" role="status" aria-hidden="true" style="width: 8px; height: 8px;"></span>
                                <span class="text-muted small" style="font-size: 0.75rem;">Sedang memproses...</span>
                            </div>
                        </div>
                    </div>
                `;
                messagesContainer.insertAdjacentHTML('beforeend', botLoading);

                // 3. Gulir ke bawah agar bubble baru terlihat
                if (chatBody) {
                    chatBody.scrollTop = chatBody.scrollHeight;
                }

                // 4. Kunci input dan tombol kirim agar tidak double submit (gunakan setTimeout agar browser tidak menolak submit)
                setTimeout(function() {
                    inputEl.value = '';
                    inputEl.readOnly = true;
                    sendBtn.disabled = true;
                }, 10);
            });
        }

        // Toggle Chat Widget (Buka / Tutup panel chat melayang)
        const toggleBtn = document.getElementById('toggle-chat-widget');
        const chatWindow = document.getElementById('chat-section');

        if (toggleBtn && chatWindow) {
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Mencegah pemicuan instant click-outside listener
                chatWindow.classList.toggle('d-none');
                if (!chatWindow.classList.contains('d-none') && chatBody) {
                    chatBody.scrollTop = chatBody.scrollHeight;
                }
            });

            // Klik di luar dialog untuk menutup
            document.addEventListener('click', function(event) {
                const isClickInsideChat = chatWindow.contains(event.target);
                const isClickInsideToggle = toggleBtn.contains(event.target);
                
                if (!isClickInsideChat && !isClickInsideToggle) {
                    chatWindow.classList.add('d-none');
                }
            });
        }
    });
</script>
@endpush
@endsection
