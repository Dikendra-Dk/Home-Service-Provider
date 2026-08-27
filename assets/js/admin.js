/**
 * SewaSathi - Admin Panel JavaScript Controller
 */

let activeVerifyProviderId = null;

function openVerificationModal(providerId, name, category, exp, docType, docPath, citizenshipNo, phone) {
    activeVerifyProviderId = providerId;
    
    document.getElementById('verifyModalName').textContent = name;
    document.getElementById('verifyModalCategory').textContent = `${category} • ${exp} Years Experience`;
    document.getElementById('verifyModalPhone').textContent = phone;
    document.getElementById('verifyModalCitizenshipNo').textContent = citizenshipNo || 'Not Provided';
    document.getElementById('verifyModalDocType').textContent = docType;
    document.getElementById('verifyModalDocImage').src = docPath;
    document.getElementById('verifyRemarks').value = '';

    const modal = document.getElementById('verifyDocumentModal');
    if (modal) modal.classList.remove('hidden');
}

function closeVerificationModal() {
    const modal = document.getElementById('verifyDocumentModal');
    if (modal) modal.classList.add('hidden');
    activeVerifyProviderId = null;
}

function submitVerification(decision) {
    if (!activeVerifyProviderId) return;

    const remarks = document.getElementById('verifyRemarks').value.trim();
    if (decision === 'rejected' && !remarks) {
        alert('Please enter a remark explaining why this document was rejected.');
        document.getElementById('verifyRemarks').focus();
        return;
    }

    const btn = document.getElementById(`btn-${decision}`);
    if (btn) btn.disabled = true;

    fetch('/api/admin.php?action=verify_provider', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            provider_id: activeVerifyProviderId,
            decision: decision,
            remarks: remarks
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, decision === 'approved' ? 'success' : 'info');
            closeVerificationModal();
            setTimeout(() => window.location.reload(), 600);
        } else {
            showToast(data.message, 'error');
            if (btn) btn.disabled = false;
        }
    })
    .catch(() => {
        showToast('Server error while updating verification.', 'error');
        if (btn) btn.disabled = false;
    });
}

function filterTableRows(inputId, tableId) {
    const query = document.getElementById(inputId).value.toLowerCase();
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    });
}
