<template>
    <button class="btn btn-lg btn-block btn-info" @click="startNewGame">
        Join game!
    </button>
</template>

<script>
  export default {
    props: {
      id: {
        type: String,
        required: true
      }
    },
    methods: {
      startNewGame() {
        axios.post(`/api/game/${this.id}/join`)
          .then(() => window.location.href = `/game/${this.id}`)
          .catch(this.handleException);
      }
    }
  }
</script>
