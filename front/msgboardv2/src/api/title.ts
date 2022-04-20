import request from "@/providers/request";

export const getTitle = () =>
  request({
    url: "/title",
    method: "get",
  });

export const changeTitle = (data: any) =>
  request({
    url: "/title",
    method: "patch",
    data,
  });
