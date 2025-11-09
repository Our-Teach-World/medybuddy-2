function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-button').forEach(el => el.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
}

function toggleEdit(section) {
    const view = document.getElementById(section + 'InfoView');
    const edit = document.getElementById(section + 'InfoEdit');

    if (view.style.display === 'none') {
        view.style.display = 'block';
        edit.style.display = 'none';
    } else {
        view.style.display = 'none';
        edit.style.display = 'block';
    }
}

// Handle Personal Info Form Submit
document.getElementById('personalInfoForm')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch(window.App.routes.updatePersonal, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.App.csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Personal information updated successfully!');
                location.reload(); // Or update DOM without reload (advanced)
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Something went wrong. Please try again.');
            console.error(error);
        });
});

// Handle Health Info Form Submit
document.getElementById('healthInfoForm')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    // Convert medications & allergies to JSON array
    let meds = formData.get('medications')?.split(',').map(m => m.trim()).filter(m => m) || [];
    let allergies = formData.get('allergies')?.split(',').map(a => a.trim()).filter(a => a) || [];

    formData.set('medical_history', JSON.stringify({
        medications: meds,
        allergies: allergies
    }));

    fetch(window.App.routes.updateHealth, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.App.csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Health information updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Something went wrong. Please try again.');
            console.error(error);
        });
});

// Hover effects
document.querySelectorAll('.appointment-item').forEach(item => {
    item.addEventListener('mouseenter', function () {
        this.style.transform = 'translateY(-2px)';
    });
    item.addEventListener('mouseleave', function () {
        this.style.transform = 'translateY(0)';
    });
});

// Profile Image Upload
const profileInput = document.getElementById('profileImageInput');
const profileForm = document.getElementById('profileImageForm');
const profilePreview = document.getElementById('profileImagePreview');

profileInput?.addEventListener('change', function () {
    if (this.files && this.files[0]) {
        // Show preview
        let reader = new FileReader();
        reader.onload = e => {
            profilePreview.src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);


        // Upload instantly
        const formData = new FormData();
        formData.append('profile_image', this.files[0]);

        fetch(window.App.routes.updateProfileImage, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": window.App.csrfToken,
                "Accept": "application/json"
            },
            body: formData
        })


            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Profile image updated successfully!");
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Upload failed, please try again.");
            });
    }
});
