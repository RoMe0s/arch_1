<template>
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label>Name:</label>
                <input class="form-control" v-model="playerName"/>
            </div>
            <div class="text-center">
                <button class="btn btn-success" @click="saveName">
                    Save name
                </button>
            </div>
        </div>
    </div>
</template>
<script>
  export default {
    props: {
      id: {
        type: String,
        required: true
      }
    },
    data() {
      return {
        playerName: null
      };
    },
    methods: {
      saveName() {
        axios.post(
          `/api/game/${this.id}/set-name`,
          {name: this.playerName}
        ).catch(this.handleException)
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
