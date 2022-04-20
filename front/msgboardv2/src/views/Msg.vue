<template>
  <div class="home">
    <h1>This is an single Message page</h1>
    <div v-if="alreadyLogin">
      <h3>From: {{ msgInfo.username }}</h3>
      <span> Content: </span>
      <span v-html="msgInfo.content"> </span>
      <span> File: {{ msgInfo.filename }} </span>
      <a
        :download="msgInfo.filename"
        :href="'data:' + msgInfo.filetype + ';base64,' + msgInfo.file"
      >
        download
      </a>
    </div>
    <div v-else>Not login yet!</div>
  </div>
</template>

<script lang="ts">
import { getMessage } from "@/api/msgs";
import { getUser } from "@/api/users";
import { Vue, Component } from "vue-property-decorator";
import { getItem, setItem } from "@/providers/cookies.ts";
import request from "@/providers/request";
import BBCodeParser from "ts-bbcode-parser";
import VueRouter, { Route } from "vue-router";

declare module "vue/types/vue" {
  interface Vue {
    $router: VueRouter;
  }
}

@Component({})
export default class extends Vue {
  private msgInfo = {
    id: "",
    username: "",
    content: "",
    filetype: "",
    filename: "",
    file: "",
  };
  private bbCodeParser = new BBCodeParser();
  private alreadyLogin = false;

  async created() {
    const token = getItem("token") as string;
    this.alreadyLogin = token.length > 0;
    if (this.alreadyLogin) {
      const msgId = this.$route.params.id;
      const reqInfo = {
        userid: getItem("userid") as string,
        token: getItem("token") as string,
      };
      try {
        const { data: msgInfo } = await getMessage(msgId, reqInfo);
        this.msgInfo = msgInfo;
        this.msgInfo.content = this.bbCodeParser.parse(this.msgInfo.content);
      } catch (err) {
        console.log(err);
      }
    }
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
