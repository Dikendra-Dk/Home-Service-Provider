/**
 * SewaSathi - Multi-Step Booking Flow Controller
 */

let bookingState = {
    step: 1,
    categoryId: 1,
    categoryName: 'Plumbing',
    providerId: null,
    providerName: 'Auto-Assign Nearest Verified Mistiri',
    serviceId: null,
    serviceName: 'Inspection & General Fix',
    servicePrice: 450.00,
    urgency: 'standard',
    urgencyFee: 0.00,
    scheduledDate: '',
    scheduledSlot: 'Morning (8:00 AM - 11:00 AM)',
    province: 'Bagmati',
    district: 'Kathmandu',
    municipality: 'Kathmandu Metropolitan City',
    wardNo: 4,
    streetAddress: '',
    landmark: '',
    problemDescription: '',
    paymentMethod: 'cash',
    totalAmount: 450.00
};

function initBooking(initialData = {}) {
    bookingState = { ...bookingState, ...initialData };
    recalculatePricing();
    updateStepUI();
}

function nextBookingStep() {
    if (!validateCurrentStep()) return;
    if (bookingState.step < 3) {
        bookingState.step++;
        updateStepUI();
        window.scrollTo({ top: 120, behavior: 'smooth' });
    }
}

function prevBookingStep() {
    if (bookingState.step > 1) {
        bookingState.step--;
        updateStepUI();
        window.scrollTo({ top: 120, behavior: 'smooth' });
    }
}

function goToStep(stepNum) {
    if (stepNum < bookingState.step || validateCurrentStep()) {
        bookingState.step = stepNum;
        updateStepUI();
    }
}

function validateCurrentStep() {
    if (bookingState.step === 1) {
        const desc = document.getElementById('problemDescription')?.value.trim();
        if (!desc || desc.length < 5) {
            showToast('Please provide a brief description of the issue (at least 5 characters).', 'error');
            document.getElementById('problemDescription')?.focus();
            return false;
        }
        bookingState.problemDescription = desc;

        // Sub-service selection
        const selectedServiceEl = document.querySelector('input[name="serviceOption"]:checked');
        if (selectedServiceEl) {
            bookingState.serviceId = selectedServiceEl.value;
            bookingState.serviceName = selectedServiceEl.dataset.name || 'Selected Service';
            bookingState.servicePrice = parseFloat(selectedServiceEl.dataset.price || 450);
        }

        // Urgency
        const urgencyEl = document.querySelector('input[name="urgency"]:checked');
        if (urgencyEl) {
            bookingState.urgency = urgencyEl.value;
            bookingState.urgencyFee = (urgencyEl.value === 'urgent_asap') ? 200.00 : 0.00;
        }
    } else if (bookingState.step === 2) {
        const dateInput = document.getElementById('scheduledDate')?.value;
        const slotInput = document.getElementById('scheduledSlot')?.value;
        const streetInput = document.getElementById('streetAddress')?.value.trim();
        const wardInput = document.getElementById('bookingWard')?.value;

        if (!dateInput) {
            showToast('Please pick a preferred service date.', 'error');
            return false;
        }
        if (!streetInput) {
            showToast('Please enter your house / tole street address.', 'error');
            document.getElementById('streetAddress')?.focus();
            return false;
        }

        bookingState.scheduledDate = dateInput;
        bookingState.scheduledSlot = slotInput;
        bookingState.streetAddress = streetInput;
        bookingState.landmark = document.getElementById('landmark')?.value.trim() || '';
        bookingState.province = document.getElementById('bookingProvince')?.value || 'Bagmati';
        bookingState.district = document.getElementById('bookingDistrict')?.value || 'Kathmandu';
        bookingState.municipality = document.getElementById('bookingMunicipality')?.value || 'Kathmandu Metropolitan City';
        bookingState.wardNo = parseInt(wardInput) || 4;
    }

    recalculatePricing();
    return true;
}

function recalculatePricing() {
    bookingState.totalAmount = bookingState.servicePrice + bookingState.urgencyFee;
    
    // Update UI pricing fields
    const baseEl = document.getElementById('summaryBasePrice');
    const urgencyEl = document.getElementById('summaryUrgencyFee');
    const totalEl = document.getElementById('summaryTotalPrice');
    const urgencyRow = document.getElementById('summaryUrgencyRow');

    if (baseEl) baseEl.textContent = `रू ${bookingState.servicePrice.toFixed(2)}`;
    if (urgencyEl) urgencyEl.textContent = `रू ${bookingState.urgencyFee.toFixed(2)}`;
    if (totalEl) totalEl.textContent = `रू ${bookingState.totalAmount.toFixed(2)}`;
    if (urgencyRow) {
        if (bookingState.urgencyFee > 0) urgencyRow.classList.remove('hidden');
        else urgencyRow.classList.add('hidden');
    }

    // Populate review summary values
    const revService = document.getElementById('revServiceName');
    const revDate = document.getElementById('revDateTime');
    const revLocation = document.getElementById('revLocation');
    const revDesc = document.getElementById('revDescription');

    if (revService) revService.textContent = bookingState.serviceName;
    if (revDate) revDate.textContent = `${bookingState.scheduledDate} • ${bookingState.scheduledSlot}`;
    if (revLocation) revLocation.textContent = `${bookingState.streetAddress}, Ward ${bookingState.wardNo}, ${bookingState.municipality}`;
    if (revDesc) revDesc.textContent = bookingState.problemDescription;
}

function updateStepUI() {
    const s1 = document.getElementById('bookingStep1');
    const s2 = document.getElementById('bookingStep2');
    const s3 = document.getElementById('bookingStep3');
    const s4 = document.getElementById('bookingStep4');

    if (!s1 || !s2 || !s3) return;

    s1.classList.add('hidden');
    s2.classList.add('hidden');
    s3.classList.add('hidden');
    if (s4) s4.classList.add('hidden');

    if (bookingState.step === 1) s1.classList.remove('hidden');
    if (bookingState.step === 2) s2.classList.remove('hidden');
    if (bookingState.step === 3) s3.classList.remove('hidden');
    if (bookingState.step === 4 && s4) s4.classList.remove('hidden');

    // Update step indicator pills
    for (let i = 1; i <= 3; i++) {
        const indicator = document.getElementById(`stepIndicator${i}`);
        const text = document.getElementById(`stepText${i}`);
        if (indicator) {
            if (i < bookingState.step) {
                indicator.className = 'w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs shadow-sm';
                indicator.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>';
            } else if (i === bookingState.step) {
                indicator.className = 'w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center font-bold text-xs ring-4 ring-rose-100 shadow-md';
                indicator.innerHTML = `${i}`;
            } else {
                indicator.className = 'w-8 h-8 rounded-full bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center font-bold text-xs';
                indicator.innerHTML = `${i}`;
            }
        }
    }
}

function submitFinalBooking() {
    const payMethodEl = document.querySelector('input[name="paymentMethod"]:checked');
    if (payMethodEl) {
        bookingState.paymentMethod = payMethodEl.value;
    }

    const submitBtn = document.getElementById('btnSubmitBooking');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Dispatching Technician...';
    }

    const payload = {
        category_id: bookingState.categoryId,
        provider_id: bookingState.providerId,
        service_id: bookingState.serviceId,
        urgency: bookingState.urgency,
        scheduled_date: bookingState.scheduledDate,
        scheduled_slot: bookingState.scheduledSlot,
        province: bookingState.province,
        district: bookingState.district,
        municipality: bookingState.municipality,
        ward_no: bookingState.wardNo,
        street_address: bookingState.streetAddress,
        landmark: bookingState.landmark,
        problem_description: bookingState.problemDescription,
        estimated_amount: bookingState.totalAmount,
        payment_method: bookingState.paymentMethod
    };

    fetch('/api/bookings.php?action=create_booking', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            bookingState.step = 4;
            const codeEl = document.getElementById('confirmedBookingCode');
            if (codeEl) codeEl.textContent = data.booking_code;
            updateStepUI();
            showToast('Service Request Dispatched to Nearby Verified Workers!', 'success');
        } else {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                showToast(data.message || 'Failed to submit booking.', 'error');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Confirm & Dispatch Booking';
                }
            }
        }
    })
    .catch(err => {
        showToast('Network error occurred. Please try again.', 'error');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Confirm & Dispatch Booking';
        }
    });
}
