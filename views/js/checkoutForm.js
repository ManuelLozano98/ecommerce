// This is your test publishable API key.
const stripe = Stripe(
  "pk_test_51TP2xfDllN9KZStgu4JTH4mUbPtEetynw47teB4OLHTNsykF6B6HMqw0qrlAwa8OrC2NLFRIpVrHFbNduudqs76L00MKZMNdvc",
);

initialize();

// Create a Checkout Session
async function initialize() {
  const urlParams = new URLSearchParams(window.location.search);
  const client = urlParams.get("client");
  if (!client || client === "undefined") {
    // notifyErrorResponse();
    location.href = "/Ecommerce";
  }

  const checkout = await stripe.initEmbeddedCheckout({
    clientSecret: client,
  });

  checkout.mount("#checkout");
}
