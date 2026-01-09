const getPlanData = (card) => ({
    type: card.dataset.planType,
    title: card.dataset.planTitle,
    price: card.dataset.planPrice,
});

const isActivationKey = (key) => key === 'Enter' || key === ' ';
const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

const buildStatusMessage = (paymentIntent) => {
    switch (paymentIntent.status) {
        case 'succeeded':
            return '支払いが完了しました。';
        case 'processing':
            return '支払いを処理しています。';
        case 'requires_payment_method':
            return '支払いに失敗しました。別のカードをお試しください。';
        default:
            return '決済の状態を確認してください。';
    }
};

export const initPaymentFlow = () => {
    const root = document.querySelector('[data-payment-page]');
    if (!root) return;

    const planCards = Array.from(root.querySelectorAll('[data-plan-card]'));
    if (!planCards.length) return;

    const summaryPlanFields = root.querySelectorAll('[data-summary-plan]');
    const summaryPriceFields = root.querySelectorAll('[data-summary-price]');
    const planInput = root.querySelector('[data-plan-input]');
    const form = root.querySelector('[data-payment-form]');
    const submitButton = root.querySelector('[data-payment-submit]');
    const errorPanel = root.querySelector('[data-payment-error]');
    const errorMessage = root.querySelector('[data-payment-error-message]');
    const statusPanel = root.querySelector('[data-payment-status]');
    const statusMessage = root.querySelector('[data-payment-status-message]');
    const paymentElementContainer = root.querySelector('[data-payment-element]');

    const stripeKey = root.dataset.stripeKey;
    const intentUrl = root.dataset.intentUrl;
    const returnUrl = root.dataset.returnUrl || window.location.href;

    let selectedPlan = getPlanData(planCards.find((card) => card.classList.contains('is-active')) || planCards[0]);
    let stripe = null;
    let elements = null;
    let paymentElement = null;
    let intentRequestId = 0;

    const setLoading = (isLoading) => {
        if (submitButton) {
            submitButton.disabled = isLoading;
        }
        if (form) {
            form.setAttribute('aria-busy', isLoading ? 'true' : 'false');
        }
    };

    const setError = (message) => {
        if (!errorPanel || !errorMessage) return;
        if (!message) {
            errorPanel.hidden = true;
            return;
        }
        errorMessage.textContent = message;
        errorPanel.hidden = false;
    };

    const setStatus = (message) => {
        if (!statusPanel || !statusMessage) return;
        if (!message) {
            statusPanel.hidden = true;
            return;
        }
        statusMessage.textContent = message;
        statusPanel.hidden = false;
    };

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

    const fetchIntent = async (planType) => {
        const csrfToken = getCsrfToken();
        const response = await fetch(intentUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            body: JSON.stringify({ plan_type: planType }),
        });

        const payload = await response.json().catch(() => ({}));
        if (!response.ok) {
            const message = payload?.error || '決済の準備に失敗しました。';
            throw new Error(message);
        }
        if (!payload?.clientSecret) {
            throw new Error('決済情報が取得できませんでした。');
        }
        return payload.clientSecret;
    };

    const mountPaymentElement = async (planType) => {
        if (!stripe || !paymentElementContainer) return;
        const requestId = ++intentRequestId;
        setLoading(true);
        setError('');
        setStatus('決済情報を準備しています。');

        try {
            const clientSecret = await fetchIntent(planType);
            if (requestId !== intentRequestId) return;

            if (paymentElement) {
                paymentElement.unmount();
            }
            elements = stripe.elements({
                clientSecret,
                appearance: {
                    theme: 'stripe',
                },
            });
            paymentElement = elements.create('payment');
            paymentElement.mount(paymentElementContainer);
            setStatus('');
        } catch (error) {
            if (requestId !== intentRequestId) return;
            setStatus('');
            setError(error.message);
        } finally {
            if (requestId === intentRequestId) {
                setLoading(false);
            }
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
        mountPaymentElement(plan.type);
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

    if (!stripeKey || !intentUrl || !paymentElementContainer) {
        setError('決済の準備ができませんでした。');
        return;
    }

    if (!window.Stripe) {
        setError('Stripeの読み込みに失敗しました。');
        return;
    }

    stripe = window.Stripe(stripeKey, { locale: 'ja' });

    const handleReturnStatus = async () => {
        const params = new URLSearchParams(window.location.search);
        const clientSecret = params.get('payment_intent_client_secret');
        if (!clientSecret) return;

        try {
            setStatus('決済結果を確認しています。');
            const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);
            if (paymentIntent) {
                setStatus(buildStatusMessage(paymentIntent));
            }
        } catch (error) {
            setError(error.message || '決済結果の確認に失敗しました。');
        } finally {
            params.delete('payment_intent_client_secret');
            params.delete('redirect_status');
            const newUrl = params.toString()
                ? `${window.location.pathname}?${params.toString()}`
                : window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    };

    handleReturnStatus();

    if (form) {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (!stripe || !elements) {
                setError('決済の準備ができていません。');
                return;
            }

            setLoading(true);
            setError('');
            setStatus('決済を処理しています。');

            try {
                const { error: submitError } = await elements.submit();
                if (submitError) {
                    throw submitError;
                }

                const { error, paymentIntent } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: returnUrl,
                    },
                    redirect: 'if_required',
                });

                if (error) {
                    throw error;
                }

                if (paymentIntent) {
                    const targetUrl = new URL(returnUrl, window.location.origin);
                    targetUrl.searchParams.set('redirect_status', paymentIntent.status || 'processing');
                    window.location.href = targetUrl.toString();
                    return;
                }
            } catch (error) {
                setStatus('');
                setError(error.message || '決済を完了できませんでした。');
            } finally {
                setLoading(false);
            }
        });
    }

    selectPlan(selectedPlan);
};
