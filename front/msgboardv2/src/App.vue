<template>
  <div>
    <div id="nav">
      <h1>Title: {{ webtitle }}</h1>
      | <router-link to="/">Home</router-link> |
      <router-link to="/Admin">Admin</router-link> |
      <router-link to="/msgs">Massages</router-link>
    </div>
    <router-view />
  </div>
</template>

<script lang="ts">
import { getTitle } from "@/api/title";
import { Vue, Component } from "vue-property-decorator";

@Component({})
export default class extends Vue {
  private webtitle = "";

  async created() {
    const { data: title } = await getTitle();
    this.webtitle = title.webtitle;
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

#nav {
  padding: 30px;
}

#nav a {
  font-weight: bold;
  color: #2c3e50;
}

#nav a.router-link-exact-active {
  color: #42b983;
}
</style>
