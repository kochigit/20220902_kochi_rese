<template>
  <div>
    {{test}}
  </div>
</template>

<script>
export default {
  data() {
    return {
      test: ""
    }
  },
  methods: {
    async getTest() {
      this.test = await this.$axios.get('http://localhost:8000/api/v1/test');
      console.log(this.test);
    }
  },
  created() {
    this.getTest();
  },
}
</script>
