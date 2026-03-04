{{-- PawCare Floating Chat Widget --}}
@if(!auth()->check() || strtolower(auth()->user()->role) === 'owner')
    <link rel="stylesheet" href="{{ asset('assets/css/chat.css') }}">

    <div id="pawcare-chat-widget">
        {{-- FAB Button --}}
        <div class="chat-fab" id="chatFab">
            <i data-lucide="message-circle"></i>
        </div>

        {{-- Chat Window --}}
        <div class="chat-window" id="chatWindow">
            <!-- Header -->
            <div class="chat-header">
                <div class="title">
                    <i data-lucide="paw-print"></i>
                    <h6>PawCare Support</h6>
                </div>
                <button class="close-btn" id="closeChat">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="chat-body" id="chatBody">
                <div class="chat-message message-ai">
                    🐾 Hello! I am the PawCare Support Assistant. You can ask me about our system, vaccinations, and rules.

                    I can also help you fill up or go to anything you want in your dash board!
                </div>
            </div>

            <div class="chat-footer">
                <form id="chatForm">
                    <div class="chat-input-row">
                        <button type="button" class="voice-btn" title="Voice Input" id="voiceBtn">
                            <i data-lucide="mic" style="width: 28px; height: 28px;"></i>
                        </button>
                        <div class="chat-input-group">
                            <input type="text" id="chatInput" placeholder="How can we help?" autocomplete="off"
                                aria-label="Support Message">
                            <button type="submit" class="send-btn" title="Send Message">
                                <i data-lucide="send"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/chat.js') }}"></script>
    <script>
        // Ensure Lucide icons are rendered if not already
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        } else {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/lucide@latest';
            script.onload = () => lucide.createIcons();
            document.head.appendChild(script);
        }
    </script>
@endif
