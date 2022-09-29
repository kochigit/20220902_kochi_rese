import Vue from 'vue';
import {
  StripePlugin,
  StripeCheckout,
  StripeElementCard,
} from '@vue-stripe/vue-stripe';

const options = {
  pk: `${ process.env.STRIPE_PK }`,
  stripeAccount: process.env.STRIPE_ACCOUNT,
  apiVersion: process.env.STRIPE_API_VERSION,
  locale: 'ja'
}
export default function () {
  Vue.component('StripeCheckout', StripeCheckout);
  Vue.component('StripeElementCard', StripeElementCard);
  Vue.use(StripePlugin, options);
}