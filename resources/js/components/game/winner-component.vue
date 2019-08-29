<template>
    <div>
        <hr>
        <h3 class="text-center">
            Winner: {{ winner.name }}
        </h3>
    </div>
</template>
<script>
    export default {
      props: {
        winner: {
          type: Object,
          required: true
        }
      }
    }
</script>
<style lang="scss" scoped>
    h2 {
        padding: 25px 0;
        margin: 0;
    }
</style>
