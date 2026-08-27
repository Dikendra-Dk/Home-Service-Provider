/**
 * SewaSathi - Provider Dashboard JavaScript Controller
 */

function toggleProviderAvailability(checkboxEl) {
    const isAvailable = checkboxEl.checked ? 1 : 0;
    const statusText = document.getElementById('providerStatusText');
    const pulseDot = document.getElementById('statusPulseDot');

    fetch('/api/provider.php?action=toggle_availability', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ is_available: isAvailable })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, isAvailable ? 'success' : 'info');
            if (statusText) {
                statusText.textContent = isAvailable ? 'Available for Jobs' : 'Currently Busy / Off-Duty';
                statusText.className = isAvailable ? 'text-xs font-bold text-emerald-700' : 'text-xs font-bold text-slate-500';
            }
            if (pulseDot) {
                pulseDot.className = isAvailable ? 'w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse' : 'w-2.5 h-2.5 rounded-full bg-slate-400';
            }
        } else {
            showToast(data.message, 'error');
            checkboxEl.checked = !checkboxEl.checked;
        }
    })
    .catch(() => {
        showToast('Failed to update status. Please retry.', 'error');
        checkboxEl.checked = !checkboxEl.checked;
    });
}

function acceptBooking(bookingId) {
    const card = document.getElementById(`request-card-${bookingId}`);
    if (confirm('Accept this booking? Customer will be notified that you are on your way.')) {
        fetch('/api/bookings.php?action=accept_booking', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                if (card) {
                    card.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            } else {
                showToast(data.message, 'error');
            }
        });
    }
}

function rejectBooking(bookingId) {
    const card = document.getElementById(`request-card-${bookingId}`);
    if (confirm('Decline this job request? It will be reassigned to other verified workers.')) {
        fetch('/api/bookings.php?action=reject_booking', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'info');
                if (card) card.remove();
            } else {
                showToast(data.message, 'error');
            }
        });
    }
}

function updateJobStatus(bookingId, status) {
    const statusLabels = {
        'in_progress': 'Mark service as In Progress (Started work on-site)?',
        'completed': 'Mark job as Completed? Invoice will be sent to customer for payment confirmation.'
    };

    if (confirm(statusLabels[status] || 'Update booking status?')) {
        fetch('/api/bookings.php?action=update_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId, status: status })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast(data.message, 'error');
            }
        });
    }
}

function saveProviderProfile(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const payload = {};
    formData.forEach((value, key) => {
        payload[key] = value;
    });

    fetch('/api/provider.php?action=update_profile', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Profile & Rate Card updated successfully!', 'success');
        } else {
            showToast(data.message, 'error');
        }
    });
}
