import request from "@/providers/request";

export const createUser = (data: any) =>
  request({
    url: "/user",
    method: "post",
    data,
  });

export const getUser = (data: any) =>
  request({
    url: "/user/get",
    method: "post",
    data,
  });

export const loginUser = (data: any) =>
  request({
    url: "/user/login",
    method: "post",
    data,
  });

export const logoutUser = (data: any) =>
  request({
    url: "/user/logout",
    method: "post",
    data,
  });
