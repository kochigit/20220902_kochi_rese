import Vue from 'vue';
import { ValidationProvider, ValidationObserver, extend, localize } from 'vee-validate';
import * as originalRules from 'vee-validate/dist/rules';
import ja from 'vee-validate/dist/locale/ja.json';
import dayjs from "dayjs";


// 全てのルールをインポート
let rule;
for (rule in originalRules) {
  extend(rule, {
    ...originalRules[rule],
  });
}

extend("after", {
  params: ["target"],
  validate: (value, params) => {
    const target = params["target"];
    const date1 = dayjs(value);
    const date2 = dayjs(target);
    if (!date1 || !date2) return false;
    return date1.isAfter(date2);
  },
  message: "{_field_}は{target}以降の日付を入力ください"
});


// 日本語に設定
localize('ja', ja);

Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);