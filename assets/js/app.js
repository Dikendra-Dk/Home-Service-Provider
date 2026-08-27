/**
 * SewaSathi - Core JavaScript Interactivity
 */

const NEPAL_LOCATIONS = {
    'Bagmati': {
        'Kathmandu': {
            'Kathmandu Metropolitan City': { totalWards: 32 },
            'Kirtipur Municipality': { totalWards: 10 },
            'Budhanilkantha Municipality': { totalWards: 13 },
            'Tokha Municipality': { totalWards: 11 }
        },
        'Lalitpur': {
            'Lalitpur Metropolitan City': { totalWards: 29 },
            'Mahalaxmi Municipality': { totalWards: 10 },
            'Godawari Municipality': { totalWards: 14 }
        },
        'Bhaktapur': {
            'Bhaktapur Municipality': { totalWards: 10 },
            'Madhyapur Thimi Municipality': { totalWards: 9 },
            'Suryabinayak Municipality': { totalWards: 10 }
        },
        'Chitwan': {
            'Bharatpur Metropolitan City': { totalWards: 29 },
            'Ratnanagar Municipality': { totalWards: 16 }
        }
    },
    'Gandaki': {
        'Kaski': {
            'Pokhara Metropolitan City': { totalWards: 33 }
        }
    },
    'Koshi': {
        'Morang': {
            'Biratnagar Metropolitan City': { totalWards: 19 }
        },
        'Sunsari': {
            'Dharan Sub-Metropolitan City': { totalWards: 20 },
            'Itahari Sub-Metropolitan City': { totalWards: 20 }
        }
    },
    'Lumbini': {
        'Rupandehi': {
            'Butwal Sub-Metropolitan City': { totalWards: 19 },
            'Siddharthanagar (Bhairahawa) Municipality': { totalWards: 13 }
        }
    }
};

// UI Toggles
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

function toggleMobileDrawer() {
    const drawer = document.getElementById('mobileDrawer');
    if (drawer) {
        drawer.classList.toggle('hidden');
    }
}

// Close dropdown on outside click
document.addEventListener('click', (e) => {
    const menuContainer = document.getElementById('userMenuContainer');
    const dropdown = document.getElementById('userDropdown');
    if (menuContainer && dropdown && !menuContainer.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

// Location Modal Controls
function openLocationModal() {
    const modal = document.getElementById('locationModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeLocationModal() {
    const modal = document.getElementById('locationModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Cascading Location Selectors
function populateSelect(selectEl, items, selectedVal = '') {
    if (!selectEl) return;
    selectEl.innerHTML = '';
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item;
        opt.textContent = item;
        if (item === selectedVal) opt.selected = true;
        selectEl.appendChild(opt);
    });
}

function onModalProvinceChange() {
    const prov = document.getElementById('modalProvince').value;
    const distSelect = document.getElementById('modalDistrict');
    const districts = Object.keys(NEPAL_LOCATIONS[prov] || {});
    populateSelect(distSelect, districts, districts[0]);
    onModalDistrictChange();
}

function onModalDistrictChange() {
    const prov = document.getElementById('modalProvince').value;
    const dist = document.getElementById('modalDistrict').value;
    const muniSelect = document.getElementById('modalMunicipality');
    const munis = Object.keys((NEPAL_LOCATIONS[prov] && NEPAL_LOCATIONS[prov][dist]) || {});
    populateSelect(muniSelect, munis, munis[0]);
    onModalMunicipalityChange();
}

function onModalMunicipalityChange() {
    const prov = document.getElementById('modalProvince').value;
    const dist = document.getElementById('modalDistrict').value;
    const muni = document.getElementById('modalMunicipality').value;
    const wardSelect = document.getElementById('modalWard');
    
    const wardData = (NEPAL_LOCATIONS[prov] && NEPAL_LOCATIONS[prov][dist] && NEPAL_LOCATIONS[prov][dist][muni]) || { totalWards: 10 };
    const totalWards = wardData.totalWards || 10;
    
    if (wardSelect) {
        wardSelect.innerHTML = '';
        for (let i = 1; i <= totalWards; i++) {
            const opt = document.createElement('option');
            opt.value = i;
            opt.textContent = `Ward ${i}`;
            wardSelect.appendChild(opt);
        }
    }
}

function saveGlobalLocation(e) {
    e.preventDefault();
    const district = document.getElementById('modalDistrict').value;
    const muni = document.getElementById('modalMunicipality').value;
    const ward = document.getElementById('modalWard').value;

    // Send AJAX to store in session
    fetch('/api/locations.php?action=set_session_location', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ district, municipality: muni, ward_no: ward })
    })
    .then(res => res.json())
    .then(data => {
        const textEl = document.getElementById('navCurrentLocationText');
        if (textEl) {
            textEl.textContent = `${district}, Ward ${ward}`;
        }
        closeLocationModal();
        showToast(`Location updated to ${muni} - Ward ${ward}`, 'success');
        // If on search page, refresh filters
        if (window.location.pathname.includes('search.php')) {
            window.location.reload();
        }
    })
    .catch(err => {
        closeLocationModal();
        showToast(`Location updated to Ward ${ward}`, 'success');
    });
}

// Toast Notifications
function showToast(message, type = 'info') {
    const existing = document.getElementById('sewasathi-toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.id = 'sewasathi-toast';
    
    let bg = 'bg-slate-900 text-white';
    if (type === 'success') bg = 'bg-emerald-600 text-white';
    if (type === 'error') bg = 'bg-rose-600 text-white';

    toast.className = `fixed bottom-20 md:bottom-6 right-6 z-50 px-4 py-3 rounded-xl shadow-2xl ${bg} text-xs font-semibold flex items-center gap-2.5 transition-all duration-300 transform translate-y-4 opacity-0`;
    toast.innerHTML = `<span>${message}</span>`;

    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
    }, 50);

    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
