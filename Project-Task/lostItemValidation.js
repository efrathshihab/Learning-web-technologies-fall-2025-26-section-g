function showError(id, message) {
    var el = document.getElementById(id);
    if (el) {
        el.innerHTML = message;
    }
}

function clearError(id) {
    var el = document.getElementById(id);
    if (el) {
        el.innerHTML = "";
    }
}

function validateLostItem() {
    var itemName      = document.getElementById("lost_item_name").value.trim();
    var description   = document.getElementById("lost_description").value.trim();
    var locationLost  = document.getElementById("lost_location").value.trim();
    var dateLost      = document.getElementById("lost_date").value.trim();
    var contactPhone  = document.getElementById("lost_phone").value.trim();

    clearError("lostItemError");

    if (itemName === "" || description === "" || locationLost === "" || dateLost === "" || contactPhone === "") {
        showError("lostItemError", "All fields are required");
        return false;
    } else if (itemName.length < 3) {
        showError("lostItemError", "Item name must be at least 3 characters");
        return false;
    } else if (description.length < 5) {
        showError("lostItemError", "Description must be at least 5 characters");
        return false;
    } else if (!/^\d{6,}$/.test(contactPhone)) {
        showError("lostItemError", "Contact phone must be at least 6 digits");
        return false;
    }

    var dateObj = new Date(dateLost);
    if (isNaN(dateObj.getTime())) {
        showError("lostItemError", "Please enter a valid date");
        return false;
    }
    var today = new Date();
    
    today.setHours(0,0,0,0);
    dateObj.setHours(0,0,0,0);
    if (dateObj > today) {
        showError("lostItemError", "Date cannot be in the future");
        return false;
    }

    
    var items = [];
    try {
        items = JSON.parse(localStorage.getItem('lostItems') || '[]');
    } catch (e) {
        items = [];
    }

    var newItem = {
        id: Date.now(),
        name: itemName,
        description: description,
        location: locationLost,
        date: dateLost,
        phone: contactPhone
    };

    items.unshift(newItem);
    localStorage.setItem('lostItems', JSON.stringify(items));

    window.location.href = 'ViewLostItems.html';
    return false;
}

