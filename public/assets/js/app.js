document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) {
        window.lucide.createIcons();
    }

    document.querySelectorAll('[data-table]').forEach((table) => {
        if (!window.DataTable || table.dataset.fmTableReady === '1') return;
        table.dataset.fmTableReady = '1';
        let order = null;
        if (table.dataset.tableOrder) {
            try {
                order = JSON.parse(table.dataset.tableOrder);
            } catch (error) {
                order = null;
            }
        } else if (table.dataset.tablePreserveOrder === '1') {
            order = [];
        }

        const options = {
            pageLength: 25,
            language: {
                search: 'ค้นหา:',
                lengthMenu: 'แสดง _MENU_ รายการ',
                info: 'แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ',
                emptyTable: 'ยังไม่มีข้อมูล',
                zeroRecords: 'ไม่พบข้อมูล',
                paginate: { first: 'แรก', last: 'ท้าย', next: 'ถัดไป', previous: 'ก่อนหน้า' }
            }
        };

        if (order !== null) {
            options.order = order;
        }

        new DataTable(table, options);
    });

    document.querySelectorAll('[data-count]').forEach((element) => {
        animateCounter(element);
    });

    document.querySelectorAll('[data-qty-action]').forEach((button) => {
        button.addEventListener('click', () => {
            const selector = button.closest('[data-qty-selector]');
            const input = selector ? selector.querySelector('input[type="number"]') : null;
            if (!input) return;
            if (button.dataset.qtyAction === 'decrement') input.stepDown();
            if (button.dataset.qtyAction === 'increment') input.stepUp();
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    document.querySelectorAll('[data-image-preview]').forEach((input) => {
        input.addEventListener('change', () => previewImage(input));
    });

    if (document.querySelector('[data-confetti]')) {
        createConfetti();
    }

    setTimeout(() => {
        document.querySelectorAll('.fm-toast').forEach((toast) => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(24px)';
            setTimeout(() => toast.remove(), 250);
        });
    }, 4800);
});

function animateCounter(element) {
    const target = Number.parseFloat(element.dataset.count || '0');
    if (!Number.isFinite(target)) return;

    const duration = 1200;
    const isMoney = element.dataset.money === 'true';
    const start = performance.now();

    function update(now) {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 4);
        const current = target * eased;

        element.textContent = isMoney ? `${current.toFixed(2)} บาท` : String(Math.round(current));

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}

function previewImage(input) {
    const targetId = input.dataset.imagePreview;
    const image = targetId ? document.getElementById(targetId) : null;
    const file = input.files && input.files[0];
    if (!image || !file) return;

    const reader = new FileReader();
    reader.addEventListener('load', () => {
        image.src = String(reader.result);
        image.hidden = false;
        image.closest('.fm-image-upload')?.classList.add('has-image');
    });
    reader.readAsDataURL(file);
}

function createConfetti() {
    const container = document.createElement('div');
    container.className = 'fm-confetti';
    document.body.appendChild(container);

    const colors = ['#6366f1', '#f59e0b', '#10b981', '#ef4444', '#06b6d4', '#8b5cf6'];

    for (let i = 0; i < 56; i += 1) {
        const piece = document.createElement('div');
        piece.className = 'fm-confetti-piece';
        piece.style.left = `${Math.random() * 100}%`;
        piece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        piece.style.animationDelay = `${Math.random() * 1.5}s`;
        piece.style.animationDuration = `${2 + Math.random() * 2}s`;
        container.appendChild(piece);
    }

    setTimeout(() => container.remove(), 5000);
}
