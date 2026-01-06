const getPlanData = (card) => ({
    type: card.dataset.planType,
    title: card.dataset.planTitle,
    price: card.dataset.planPrice,
});

const isActivationKey = (key) => key === 'Enter' || key === ' ';

export const initPaymentFlow = () => {
    const root = document.querySelector('[data-payment-page]');
    if (!root) return;

    const planCards = Array.from(root.querySelectorAll('[data-plan-card]'));
    if (!planCards.length) return;

    const summaryPlanFields = root.querySelectorAll('[data-summary-plan]');
    const summaryPriceFields = root.querySelectorAll('[data-summary-price]');
    const planInput = root.querySelector('[data-plan-input]');

    let selectedPlan = getPlanData(planCards.find((card) => card.classList.contains('is-active')) || planCards[0]);

    const updateSummary = (plan) => {
        summaryPlanFields.forEach((node) => {
            node.textContent = plan.title;
        });
        summaryPriceFields.forEach((node) => {
            node.textContent = plan.price;
        });
        if (planInput) {
            planInput.value = plan.type;
        }
    };

    const selectPlan = (plan) => {
        selectedPlan = plan;
        planCards.forEach((card) => {
            const isActive = card.dataset.planType === plan.type;
            card.classList.toggle('is-active', isActive);
            card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
        updateSummary(plan);
    };

    planCards.forEach((card) => {
        const plan = getPlanData(card);

        const applySelection = () => selectPlan(plan);

        card.addEventListener('click', applySelection);
        card.addEventListener('keydown', (event) => {
            if (!isActivationKey(event.key)) return;
            event.preventDefault();
            applySelection();
        });

        const chooseButton = card.querySelector('[data-plan-select]');
        if (chooseButton) {
            chooseButton.addEventListener('click', (event) => {
                event.stopPropagation();
                applySelection();
            });
        }
    });

    selectPlan(selectedPlan);
};
