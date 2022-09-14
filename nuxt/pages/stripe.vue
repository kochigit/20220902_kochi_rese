<template>
  <div class="stripe">
    <div id="card-number" class="text-dark-800 focus:ring-2 border-input border outline-none px-4 rounded text-sm py-3" />
    <div id="card-expiry" class="text-dark-800 focus:ring-2 border-input border outline-none px-4 rounded text-sm py-3" />
    <div id="card-cvc" class="text-dark-800 focus:ring-2 border-input border outline-none px-4 rounded text-sm py-3" />
    <div>{{ cardErrorMessage }}</div>
    <div id="card-element"></div>
    <button @click.prevent="submit">SUBMIT</button>

    
    <stripe-element-card
      :pk="pk"
    ></stripe-element-card>

    <script
      src="https://checkout.stripe.com/checkout.js" class="stripe-button"
      :data-key="pk"
      data-amount="1000"
      data-name="TEST"
      data-description="TESTTEST"
      data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
      data-locale="ja"
      data-currency="jpy"
    >
    </script>
  </div>
</template>

<script>
export default {
  data() {
    return {
      pk: process.env.STRIPE_PK,
      cardElement: null,
      cardErrorMessage: null,
      loading: false,
      lineItems: [
        {
          price: 'price_1Lhr0yCouIoZ646aaDKpgCiG', // The id of the one-time price you created in your Stripe dashboard
          quantity: 1,
        },
      ],
      successURL: 'http://localhost:3000',
      cancelURL: 'http://localhost:3000/404',
      
    }
  },
  
  methods: {
    async submit() {
      // TODO 以下を参考にしつつ、paymentの実行をします
      // https://stripe.com/docs/billing/subscriptions/fixed-price
      this.$nuxt.$loading.start()
      try {
        // 支払い方法を作成
        const paymentMethod = await this.$stripe.createPaymentMethod({
          type: 'card',
          card: this.cardElement,
        })
        if (paymentMethod.error) {
          this.cardErrMessage = paymentMethod.error.message || 'カード番号が無効です。';
          return false;
        } else {
          this.cardErrMessage = ''
        }
        console.log(paymentMethod.paymentMethod);
        
        const response = await this.$axios.post('/auth/payment', paymentMethod.paymentMethod)
        console.log(response);
        // 支払い方法が作成されたので、その支払い方法のIDを使って支払い処理をおこないます。
        // ...以下略
      } catch(error) {
        alert(error)
      } finally {
        this.$nuxt.$loading.finish()
      }
    },
    showCardError(event) {
      if (event.error) {
        this.cardErrorMessage = event.error.message
      } else {
        this.cardErrorMessage = ''
      }
    },
  },
  created() {
  },
  mounted() {
    const style = {
      base: {
        color: 'black',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '14px',
        '::placeholder': {
          color: '#aab7c4'
        }
      },
      invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
      }
    }
    const stripeElements = this.$stripe.elements();
    this.cardNumber = stripeElements.create('cardNumber', { style });
    this.cardNumber.mount('#card-number');
    this.cardExpiry = stripeElements.create('cardExpiry', { style });
    this.cardExpiry.mount('#card-expiry');
    this.cardCvc = stripeElements.create('cardCvc', { style });
    this.cardCvc.mount('#card-cvc');

    const elements = this.$stripe.elements();
    this.cardElement = elements.create('card');
    this.cardElement.mount('#card-element');
    this.cardElement.on('change', this.showCardError);
  },
  beforeDestroy () {
    this.cardNumber.destroy();
    this.cardExpiry.destroy();
    this.cardCvc.destroy();
    this.cardElement.destroy();
  }
}
</script>