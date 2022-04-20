<template>
  <div class="admin">
    <h1>This is an admin page</h1>
    <div v-if="showForm">
      admin!
      <el-form :inline="true" :model="titleInput" class="title-form-inline">
        <el-form-item label="New Title:">
          <el-input
            v-model="titleInput.newtitle"
            placeholder="new title"
          ></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="onSubmit">Change!</el-button>
        </el-form-item>
      </el-form>
      <span> {{ Changeresponse }} </span>
    </div>
    <div v-else>nope, you're not admin.</div>
  </div>
</template>

<script lang="ts">
import { changeTitle } from "@/api/title";
import { Vue, Component } from "vue-property-decorator";
import { getItem } from "@/providers/cookies.ts";

@Component({})
export default class extends Vue {
  private titleInput = {
    newtitle: "",
  };
  private Changeresponse = "";
  private showForm = false;

  async created() {
    const reqInfo = {
      userid: getItem("userid") as string,
      token: getItem("token") as string,
    };
    try {
      const { data: title } = await changeTitle(reqInfo);
      this.showForm = true;
    } catch (err) {
      console.log(err);
    }
    console.log(this.showForm);
  }

  private async onSubmit() {
    const reqInfo = {
      userid: getItem("userid") as string,
      token: getItem("token") as string,
      newtitle: this.titleInput.newtitle,
    };
    try {
      const { data: title } = await changeTitle(reqInfo);
      this.Changeresponse = title;
      this.showForm = true;
    } catch (err) {
      this.Changeresponse = err;
      console.log(err);
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
