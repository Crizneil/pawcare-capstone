/* PawCare Floating Chat Widget Logic */

document.addEventListener('DOMContentLoaded', function () {
    const chatFab = document.getElementById('chatFab');
    const chatWindow = document.getElementById('chatWindow');
    const closeChat = document.getElementById('closeChat');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatBody = document.getElementById('chatBody');

    const voiceBtn = document.getElementById('voiceBtn');

    // Toggle Chat
    if (chatFab) {
        chatFab.addEventListener('click', () => {
            chatWindow.classList.toggle('active');
            if (chatWindow.classList.contains('active')) {
                chatInput.focus();
            }
        });
    }

    if (closeChat) {
        closeChat.addEventListener('click', () => {
            chatWindow.classList.remove('active');
        });
    }

    // --- Web Speech API Integration ---
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (SpeechRecognition && voiceBtn) {
        const recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.lang = 'en-US';
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;

        voiceBtn.addEventListener('click', () => {
            try {
                recognition.start();
                voiceBtn.style.color = '#ff0000'; // Red for recording
                voiceBtn.classList.add('recording');
                chatInput.placeholder = "Listening... Speak now 🐾";
            } catch (e) {
                console.error('Speech recognition error:', e);
            }
        });

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            chatInput.value = transcript;
            voiceBtn.style.color = '#ff8c00';
            voiceBtn.classList.remove('recording');
            chatInput.placeholder = "How can we help?";
            // Auto-focus input after speech
            chatInput.focus();
        };

        recognition.onspeechend = () => {
            recognition.stop();
            voiceBtn.style.color = '#ff8c00';
            voiceBtn.classList.remove('recording');
        };

        recognition.onerror = (event) => {
            console.error('OCR Error:', event.error);
            voiceBtn.style.color = '#ff8c00';
            voiceBtn.classList.remove('recording');
            chatInput.placeholder = "Mic error. Try typing instead.";
        };
    } else if (voiceBtn) {
        // Fallback for unsupported browsers
        voiceBtn.addEventListener('click', () => {
            alert("Voice input is not supported in this browser. Please use a modern browser like Chrome.");
        });
    }

    // --- Text-to-Speech (TTS) ---
    function speakText(text) {
        if ('speechSynthesis' in window) {
            // Cancel any ongoing speech
            window.speechSynthesis.cancel();

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'en-US';
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            window.speechSynthesis.speak(utterance);
        }
    }

    // --- Generic Auto-Fill DOM Logic ---
    function genericAutoFillForm(data) {
        // Attempt to open the most relevant modal if closed
        // E.g., Pet Record Modal or Appointment Modal
        const possibleModals = ['setAppointmentModal', 'addPetModal'];
        let modalOpened = false;
        for (let modalId of possibleModals) {
            const modalEl = document.getElementById(modalId);
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
                modalOpened = true;
                break; // open the first one we find on this page
            }
        }

        // Iterate through all keys in the JSON data and try to find matching inputs
        for (const [key, value] of Object.entries(data)) {
            // Try specific names first (e.g. pet_id vs pet_name)
            let inputs = document.querySelectorAll(`input[name="${key}"], select[name="${key}"], textarea[name="${key}"]`);

            // Helpful fallbacks based on common names
            if (inputs.length === 0 && key === 'pet_name') {
                inputs = document.querySelectorAll(`select[name="pet_id"]`);
            } else if (inputs.length === 0 && key === 'service') {
                inputs = document.querySelectorAll(`select[name="service_type"]`);
            } else if (inputs.length === 0 && key === 'date') {
                inputs = document.querySelectorAll(`input[name="appointment_date"], input[type="date"]`);
            } else if (inputs.length === 0 && key === 'time') {
                inputs = document.querySelectorAll(`select[name="appointment_time"], input[type="time"]`);
            }

            // Fill matching inputs
            inputs.forEach(input => {
                if (input.tagName.toLowerCase() === 'select') {
                    // Try exact value
                    input.value = String(value).toLowerCase();
                    // If not set, try to match text
                    if (!input.value) {
                        for (let i = 0; i < input.options.length; i++) {
                            if (input.options[i].text.toLowerCase().includes(String(value).toLowerCase())) {
                                input.selectedIndex = i;
                                break;
                            }
                        }
                    }
                    // If still not set and it's time, add it
                    if (!input.value && (key === 'time' || key === 'appointment_time')) {
                        const newOpt = new Option(value + " (Requested)", value, true, true);
                        input.append(newOpt);
                    }
                } else {
                    input.value = value;
                }
            });

            // Specific UI visual updates for PawCare appointments
            if (key === 'date') {
                const displayDate = document.getElementById('appointmentScheduleDisplay');
                if (displayDate) displayDate.innerText = value + " (Auto-filled)";
            }
        }
    }

    // Handle Sending Messages (Live AI Integration)
    if (chatForm) {
        chatForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const message = chatInput.value.trim();

            if (message) {
                appendMessage(message, 'user');
                chatInput.value = '';

                // Show "Thinking" state
                const thinkingId = 'thinking-' + Date.now();
                appendMessage("...", 'ai', thinkingId);

                // Fetch response from Laravel Backend
                fetch('/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: message })
                })
                    .then(response => response.json())
                    .then(data => {
                        const thinkingMsg = document.getElementById(thinkingId);
                        if (thinkingMsg) thinkingMsg.remove();

                        if (data.reply) {
                            let aiText = data.reply;

                            // Check for SYSTEM_NAV token
                            if (aiText.includes('[SYSTEM_NAV]')) {
                                const parts = aiText.split('[SYSTEM_NAV]');
                                const preText = parts[0].trim();
                                const jsonText = parts[1].trim();

                                if (preText) {
                                    appendMessage(preText, 'ai');
                                    speakText(preText);
                                }

                                try {
                                    const navData = JSON.parse(jsonText);
                                    if (navData.target) {
                                        setTimeout(() => {
                                            window.location.href = `/pet-owner/${navData.target}`;
                                        }, 1500); // Give the AI a moment to speak before redirecting
                                    }
                                } catch (e) {
                                    console.error("Error parsing nav JSON", e);
                                }
                            }
                            // Check for AUTO_FILL token
                            else if (aiText.includes('[AUTO_FILL]')) {
                                // Split the text and JSON
                                const parts = aiText.split('[AUTO_FILL]');
                                const preText = parts[0].trim();
                                const jsonText = parts[1].trim();

                                if (preText) {
                                    appendMessage(preText, 'ai');
                                    speakText(preText);
                                }

                                try {
                                    const fillData = JSON.parse(jsonText);
                                    genericAutoFillForm(fillData);
                                } catch (e) {
                                    console.error("Error parsing auto-fill JSON", e);
                                }
                            } else {
                                // Normal AI chat
                                appendMessage(aiText, 'ai');
                                speakText(aiText);
                            }
                        } else {
                            const errorMsg = "I'm sorry, I'm having trouble connecting to the Meycauayan server. Please try again later.";
                            appendMessage(errorMsg, 'ai');
                            speakText(errorMsg);
                        }
                    })
                    .catch(error => {
                        console.error('Chat Error:', error);
                        const thinkingMsg = document.getElementById(thinkingId);
                        if (thinkingMsg) thinkingMsg.remove();

                        const errorMsg = "Connection error. Please visit the City Hall Veterinary Office for assistance.";
                        appendMessage(errorMsg, 'ai');
                        speakText(errorMsg);
                    });
            }
        });
    }

    function appendMessage(text, sender, id = null) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-message message-${sender}`;
        if (id) msgDiv.id = id;

        // Convert line breaks to <br> for better readability
        msgDiv.innerHTML = text.replace(/\n/g, '<br>');

        chatBody.appendChild(msgDiv);

        // Scroll to bottom
        chatBody.scrollTop = chatBody.scrollHeight;
    }
});
