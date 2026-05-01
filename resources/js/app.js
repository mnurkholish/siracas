import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

const revealElements = document.querySelectorAll("[data-reveal]");

if (revealElements.length > 0) {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add("is-visible");
                observer.unobserve(entry.target);
            });
        },
        {
            threshold: 0.16,
            rootMargin: "0px 0px -60px 0px",
        },
    );

    revealElements.forEach((element) => observer.observe(element));
}
