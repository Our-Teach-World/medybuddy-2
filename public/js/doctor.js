// Init Swiper slider
const swiper = new Swiper('#categorySwiper', {
    slidesPerView: 'auto',
    spaceBetween: 10,
    freeMode: true,
    grabCursor: true,
});

document.addEventListener("DOMContentLoaded", () => {
    const searchForm = document.getElementById("searchForm");
    const specializationInput = document.getElementById("specializationInput");
    const resultsWrapper = document.getElementById("resultsWrapper");
    const qInput = document.getElementById("q");
    const clearBtn = document.getElementById("clearSearch");

    // 🔹 Fetch results via AJAX
    function fetchResults(url = null) {
        const params = new URLSearchParams(new FormData(searchForm)).toString();
        const targetUrl = url ? url : (searchForm.action + "?" + params);

        fetch(targetUrl, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
            .then(res => res.json())
            .then(data => {
                resultsWrapper.innerHTML = data.html;
                bindPagination(); // re-bind events after DOM update
            })
            .catch(err => console.error("Fetch error:", err));
    }

    // 🔹 Category click
    document.querySelectorAll(".category-card").forEach(btn => {
        btn.addEventListener("click", function () {
            document.querySelectorAll(".category-card").forEach(b => b.classList.remove("active"));
            this.classList.add("active");
            specializationInput.value = this.dataset.category || "All";
            fetchResults();
        });
    });

    // 🔹 Clear search button
    clearBtn.addEventListener("click", function () {
        qInput.value = "";
        fetchResults();
    });

    // 🔹 Live search typing (debounced)
    let typingTimer;
    qInput.addEventListener("input", function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(fetchResults, 400);
    });

    // 🔹 Pagination AJAX
    function bindPagination() {
        document.querySelectorAll("#resultsWrapper .pagination a").forEach(link => {
            link.addEventListener("click", e => {
                e.preventDefault();
                fetchResults(link.href);
            });
        });
    }

    bindPagination();
});
