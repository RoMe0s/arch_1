<template>
    <div>
        <div v-if="loaded">
            <div class="row">
                <div class="col-md-6">
                    <h2>You:</h2>
                    {{ player.name || 'N/A' }}
                </div>
                <div class="col-md-6 text-right" v-if="competitor">
                    <h2>Competitor:</h2>
                    {{ competitor.name || 'N/A' }}
                </div>
            </div>
            <br>
            <div class="row">
                <h3 class="col-md-6">Started at: {{ game.startedAt || 'N/A' }}</h3>
                <h3 class="col-md-6 text-right">Ended at: {{ game.endedAt || 'N/A' }}</h3>
            </div>

            <winner-component v-if="game.winner" :winner="game.winner"/>

            <set-name-component :id="id" v-if="!player.name"/>

            <waiting-competitor-component v-if="player.name && (!competitor || !competitor.name)"/>

            <div v-if="player.name && competitor && competitor.name">
                <hr>
                <div class="game row" v-for="x in 3">
                    <div class="col-md-4 button-wrapper" v-for="y in 3">
                        <button class="btn btn-block btn-primary" v-if="hasMark(x - 1, y -1)" :disabled="game.winner || game.endedAt">
                            {{ getMarkLabel(x - 1, y - 1) }}
                        </button>
                        <button class="btn btn-block btn-info" v-else @click="makeAMove(x - 1, y - 1)"
                                :disabled="game.winner || game.endedAt">
                        </button>
                    </div>
                    <br>
                </div>
            </div>
        </div>
        <loading-component v-else/>
    </div>
</template>

<script>
  import LoadingComponent from './game/loading-component';
  import WaitingCompetitorComponent from './game/waiting-competitor-component';
  import WinnerComponent from './game/winner-component';
  import SetNameComponent from './game/set-name-component';

  export default {
    components: {
      LoadingComponent,
      WaitingCompetitorComponent,
      WinnerComponent,
      SetNameComponent
    },
    props: {
      id: {
        type: String,
        required: true
      }
    },
    data() {
      return {
        game: {
          owner: null,
          competitor: null,
          winner: null,
          startedAt: null,
          endedAt: null,
          stepsCount: null
        },
        player: {
          name: null,
          steps: []
        },
        competitor: {
          name: null,
          steps: []
        },
        loaded: false
      };
    },
    methods: {
      hasMark(x, y) {
        const playerStepIndex = this.player.steps.findIndex(step => step.x == x && step.y == y);
        if (playerStepIndex >= 0) {
          return true;
        }
        return this.competitor.steps.findIndex(step => step.x == x && step.y == y) >= 0;
      },
      getMarkLabel(x, y) {
        if (this.player.steps.findIndex(step => step.x == x && step.y == y) >= 0) {
          return 'X';
        }
        return '0';
      },
      makeAMove(x, y) {
        axios.post(
          `/api/game/${this.id}/move`,
          {x, y}
        ).catch(this.handleException)
      }
    },
    created() {
      const refreshData = () => axios.get(`/api/game/${this.id}`)
        .then(response => {
          this.game = response.data.game;
          this.player = response.data.player;
          this.competitor = response.data.competitor;
          this.loaded = true;
        })
        .catch(this.handleException);

      setInterval(refreshData, 1000);
      refreshData();
    }
  }
</script>

<style lang="scss" scoped>
    .game {
        width: 725px;
        margin: 0 auto;

        .button-wrapper button {
            width: 200px;
            height: 200px;
            font-size: 100px;
            line-height: 100px;
            margin: 25px;
        }
    }
</style>
