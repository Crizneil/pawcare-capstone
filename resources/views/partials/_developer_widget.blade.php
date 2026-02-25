<!-- Floating Developer Widget -->
<div id="dev-widget" class="dev-widget">
    <button id="dev-toggle" class="dev-toggle" title="Developer Assistant">
        <i class="ti-user"></i>
    </button>
    <div id="dev-panel" class="dev-panel">
        <div class="dev-header">
            <h3>Developer Team</h3>
            <button id="dev-close" class="dev-close">&times;</button>
        </div>
        <div class="dev-content">
            <ul class="dev-list">
                <li>
                    <a href="https://web.facebook.com/alpha.criz" target="_blank">
                        <i class="ti-facebook"></i> <strong>Crizneil</strong> - Full Stack & UI/UX Designer
                    </a>
                </li>
                <li>
                    <a href="https://web.facebook.com/ejramos28" target="_blank">
                        <i class="ti-facebook"></i> <strong>Edrine</strong> - Full Stack & QA
                    </a>
                </li>
                <li>
                    <a href="https://web.facebook.com/ANGELO.HOMIEZYD.ADVINCULA" target="_blank">
                        <i class="ti-facebook"></i> <strong>Angelo</strong> - Full Stack & ST
                    </a>
                </li>
                <li>
                    <a href="https://web.facebook.com/marvin.cinco.752" target="_blank">
                        <i class="ti-facebook"></i> <strong>Marvin</strong> - Front End & SA
                    </a>
                </li>
            </ul>
            <hr>
            <div class="dev-contact">
                <h4>Send a Message</h4>
                <form id="dev-contact-form">
                    @csrf
                    <div class="mb-2">
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Your Name"
                            required>
                    </div>
                    <div class="mb-2">
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="Your Email"
                            required>
                    </div>
                    <div class="mb-2">
                        <textarea name="message" class="form-control form-control-sm" rows="3"
                            placeholder="How can we help?" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-orange btn-sm w-100">Send Message</button>
                    <div id="dev-form-status" class="mt-2" style="display:none; font-size: 0.8rem;"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .dev-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
    }

    .dev-toggle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #FF7D44;
        border: none;
        color: #fff;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dev-toggle:hover {
        transform: scale(1.1);
        background: #e66a35;
    }

    .dev-panel {
        position: fixed;
        right: -350px;
        bottom: 85px;
        width: 320px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
        transition: right 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden;
        border: 1px solid rgba(255, 125, 68, 0.2);
    }

    .dev-panel.active {
        right: 20px;
    }

    .dev-header {
        background: #FF7D44;
        color: #fff;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dev-header h3 {
        margin: 0;
        font-size: 1.1rem;
        color: #fff;
    }

    .dev-close {
        background: none;
        border: none;
        color: #fff;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .dev-content {
        padding: 20px;
        color: #444;
    }

    .dev-list {
        list-style: none;
        padding: 0;
        margin-bottom: 15px;
    }

    .dev-list li a {
        color: #444;
        text-decoration: none;
        display: block;
        transition: color 0.2s;
    }

    .dev-list li a:hover {
        color: #FF7D44;
    }

    .dev-list li a strong {
        color: #FF7D44;
    }

    .dev-list li:last-child {
        border-bottom: none;
    }

    .dev-social a {
        color: #3b5998;
        text-decoration: none;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .dev-social a i {
        margin-right: 5px;
    }

    .dev-contact h4 {
        font-size: 1rem;
        margin: 15px 0 10px;
        color: #FF7D44;
    }

    .btn-orange {
        background: #FF7D44;
        border: none;
        color: #fff;
    }

    .btn-orange:hover {
        background: #e66a35;
        color: #fff;
    }

    .text-orange {
        color: #FF7D44;
    }

    /* Dashboard compatibility fix */
    #sidebar .mt-auto {
        margin-bottom: 20px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('dev-toggle');
        const panel = document.getElementById('dev-panel');
        const close = document.getElementById('dev-close');
        const form = document.getElementById('dev-contact-form');
        const status = document.getElementById('dev-form-status');

        toggle.addEventListener('click', () => panel.classList.toggle('active'));
        close.addEventListener('click', () => panel.classList.remove('active'));

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            status.style.display = 'block';
            status.innerHTML = '<span class="text-info">Sending...</span>';

            fetch('{{ route("contact.developer") }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        status.innerHTML = '<span class="text-success">' + data.message + '</span>';
                        form.reset();
                        setTimeout(() => {
                            status.style.display = 'none';
                            panel.classList.remove('active');
                        }, 3000);
                    } else {
                        status.innerHTML = '<span class="text-danger">Failed to send.</span>';
                    }
                })
                .catch(() => {
                    status.innerHTML = '<span class="text-danger">Error occurred.</span>';
                });
        });
    });
</script>