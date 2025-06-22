import React, { createContext, useState, useContext, useCallback } from "react";

const UserContext = createContext(null);
export const useUser = () => useContext(UserContext);

export function UserProvider({ children }) {
  const [user, setUser] = useState(() => {
    const saved = localStorage.getItem("user");
    return saved ? JSON.parse(saved) : null;
  });

  const [openChatWindows, setOpenChatWindows] = useState([]);
  const [isChatBarVisible, setIsChatBarVisible] = useState(false);

  const openChatWithUser = useCallback(
    (chatUser) => {
      if (!isChatBarVisible) setIsChatBarVisible(true);
      if (openChatWindows.some((u) => u.id === chatUser.id)) return;
      setOpenChatWindows((prev) =>
        prev.length >= 3 ? [...prev.slice(1), chatUser] : [...prev, chatUser]
      );
    },
    [openChatWindows, isChatBarVisible]
  );

  const closeChatWindow = useCallback((userId) => {
    setOpenChatWindows((prev) => prev.filter((u) => u.id !== userId));
  }, []);
  // ----------------------------------------

  const login = (userData, token) => {
    localStorage.setItem("user", JSON.stringify(userData));
    localStorage.setItem("token", token);
    setUser(userData);
  };

  const logout = () => {
    localStorage.clear();
    setUser(null);
    setOpenChatWindows([]);
  };

  const value = {
    user,
    login,
    logout,
    openChatWindows,
    openChatWithUser,
    closeChatWindow,
    isChatBarVisible,
    setIsChatBarVisible,
  };

  return <UserContext.Provider value={value}>{children}</UserContext.Provider>;
}
