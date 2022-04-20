import Cookies from 'js-cookie'

// What may be used: token, userid

// Cookie
export const getItem = (key: string) => Cookies.get(key)
export const setItem = (key: string, token: string) => {
  Cookies.set(key, token)
}
export const removeItem = (key: string) => Cookies.remove(key)
