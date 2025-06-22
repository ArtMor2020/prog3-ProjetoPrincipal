import React, { useState, useEffect, useCallback } from "react";
import axios from "axios";
import { MessageSquare, ChevronUp, ChevronDown } from "lucide-react";
import { useUser } from "../contexts/UserContext";
import ChatWindow from "./ChatWindow";

export default function ChatBar() {
  const {
    user,
    openChatWindows,
    openChatWithUser,
    closeChatWindow,
    isChatBarVisible,
    setIsChatBarVisible,
  } = useUser();
  const [friends, setFriends] = useState([]);
  const [loading, setLoading] = useState(true);
  const [unreadSummary, setUnreadSummary] = useState({});

  const fetchFriends = useCallback(async () => {
    if (!user) return;
    setLoading(true);
    try {
      const { data } = await axios.get(
        `http://localhost:8080/friendship/friends/${user.id}`
      );
      const friendPromises = data.map((friendship) => {
        const friendId =
          friendship.id_user1 === user.id
            ? friendship.id_user2
            : friendship.id_user1;
        return axios.get(`http://localhost:8080/users/${friendId}`);
      });
      const friendResponses = await Promise.all(friendPromises);
      const friendData = friendResponses.map((res) => res.data);
      setFriends(friendData);
    } catch (error) {
      console.error("Erro ao buscar amigos:", error);
    } finally {
      setLoading(false);
    }
  }, [user]);

  const fetchUnreadSummary = useCallback(async () => {
    if (!user) return;
    try {
      const { data } = await axios.get(
        `http://localhost:8080/direct-messages/unread-summary/${user.id}`
      );
      const summaryMap = data.reduce((acc, item) => {
        acc[item.id_sender] = item.unread_count;
        return acc;
      }, {});
      setUnreadSummary(summaryMap);
    } catch (error) {
      console.error("Erro ao buscar resumo de não lidas:", error);
    }
  }, [user]);

  useEffect(() => {
    fetchFriends();
    fetchUnreadSummary();
    const interval = setInterval(fetchUnreadSummary, 10000);
    return () => clearInterval(interval);
  }, [fetchFriends, fetchUnreadSummary]);

  const handleOpenChat = (friend) => {
    openChatWithUser(friend);
    if (unreadSummary[friend.id]) {
      const newSummary = { ...unreadSummary };
      delete newSummary[friend.id];
      setUnreadSummary(newSummary);
    }
  };

  if (!user) {
    return null;
  }

  return (
    <>
      <div className="fixed bottom-0 right-0 flex items-end z-40">
        {openChatWindows.map((chatUser) => (
          <ChatWindow
            key={chatUser.id}
            chatUser={chatUser}
            onClose={() => closeChatWindow(chatUser.id)}
          />
        ))}

        <div className="w-64 bg-white rounded-t-lg shadow-xl border border-gray-300">
          <div
            className="flex justify-between items-center p-3 bg-blue-600 text-white rounded-t-lg cursor-pointer"
            onClick={() => setIsChatBarVisible(!isChatBarVisible)}
          >
            <div className="flex items-center">
              <MessageSquare size={20} className="mr-2" />
              <span className="font-bold">Mensagens</span>
            </div>
            {isChatBarVisible ? (
              <ChevronDown size={20} />
            ) : (
              <ChevronUp size={20} />
            )}
          </div>

          {isChatBarVisible && (
            <div className="h-96 flex flex-col">
              <div className="flex-1 overflow-y-auto">
                {loading ? (
                  <p className="p-3 text-gray-500 text-center">Carregando...</p>
                ) : friends.length > 0 ? (
                  friends.map((friend) => (
                    <div
                      key={friend.id}
                      onClick={() => handleOpenChat(friend)}
                      className="flex items-center justify-between p-2 hover:bg-gray-100 cursor-pointer border-b"
                    >
                      <div className="flex items-center">
                        <div className="w-8 h-8 bg-gray-300 rounded-full mr-3"></div>
                        <span>{friend.name}</span>
                      </div>
                      {unreadSummary[friend.id] && (
                        <span className="flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-xs text-white">
                          {unreadSummary[friend.id]}
                        </span>
                      )}
                    </div>
                  ))
                ) : (
                  <p className="p-3 text-gray-500 text-center">
                    Adicione amigos para conversar.
                  </p>
                )}
              </div>
            </div>
          )}
        </div>
      </div>
    </>
  );
}
