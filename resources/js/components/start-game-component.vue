<template>
    <button class="btn btn-lg btn-block btn-info" @click="startNewGame">
        Start new game!
    </button>
</template>

<script>
  export default {
    methods: {
      startNewGame() {
        axios.post('/api/game/start')
          .then(response => window.location.href = `/game/${response.data.id}`)
          .catch(this.handleException);
      }
    }
  }
</script>
