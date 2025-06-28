import React, { useState, useEffect, useRef } from "react";
import axios from "axios";
import { X, Send } from "lucide-react";
import { useUser } from "../contexts/UserContext";
import timeAgo from "../utils/timeAgo";
import apiClient from '../api/axiosConfig';

export default function ChatWindow({ chatUser, onClose }) {
  const { user } = useUser();
  const [messages, setMessages] = useState([]);
  const [newMessage, setNewMessage] = useState("");
  const [isExpanded, setIsExpanded] = useState(true);
  const messagesEndRef = useRef(null);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: "auto" });
  };

  const fetchMessages = async () => {
    try {
      const { data } = await apiClient.get(
        `http://localhost:8080/direct-messages/conversation/${user.id}/${chatUser.id}`
      );
      setMessages(data);
    } catch (error) {
      console.error("Erro ao buscar mensagens:", error);
    }
  };

  useEffect(() => {
    const markAsRead = async () => {
      try {
        await apiClient.put(
          `http://localhost:8080/direct-messages/mark-conversation-seen/${user.id}/${chatUser.id}`
        );
      } catch (error) {
        console.error("Erro ao marcar mensagens como lidas:", error);
      }
    };

    markAsRead();
    fetchMessages();

    const interval = setInterval(fetchMessages, 5000);
    return () => clearInterval(interval);
  }, [user.id, chatUser.id]);

  useEffect(() => {
    scrollToBottom();
  }, [messages]);

  const handleSendMessage = async (e) => {
    e.preventDefault();
    if (!newMessage.trim()) return;

    const payload = {
      id_sender: user.id,
      id_reciever: chatUser.id,
      content: newMessage.trim(),
    };

    try {
      const tempMessage = {
        id: Date.now(),
        id_sender: user.id,
        content: newMessage.trim(),
        sent_at: {
          date: new Date().toISOString().slice(0, 19).replace("T", " "),
        },
      };
      setMessages((prev) => [...prev, tempMessage]);
      setNewMessage("");

      await apiClient.post("http://localhost:8080/direct-messages", payload);
      fetchMessages();
    } catch (error) {
      console.error("Erro ao enviar mensagem:", error);
      setMessages((prev) => prev.filter((m) => m.id !== tempMessage.id));
    }
  };

  return (
    <div className="w-72 flex flex-col bg-white rounded-t-lg shadow-xl border border-gray-300 ml-4">
      <div
        className="flex justify-between items-center p-2 bg-blue-600 text-white rounded-t-lg cursor-pointer"
        onClick={() => setIsExpanded(!isExpanded)}
      >
        <span className="font-bold">{chatUser.name}</span>
        <button
          onClick={(e) => {
            e.stopPropagation();
            onClose();
          }}
          className="hover:bg-blue-500 p-1 rounded-full"
        >
          <X size={18} />
        </button>
      </div>

      {isExpanded && (
        <>
          <div className="flex-1 p-3 h-80 overflow-y-auto bg-gray-50">
            {messages.map((msg) => (
              <div
                key={msg.id}
                className={`flex mb-3 ${
                  msg.id_sender == user.id ? "justify-end" : "justify-start"
                }`}
              >
                <div
                  className={`max-w-xs rounded-lg px-3 py-2 ${
                    msg.id_sender == user.id
                      ? "bg-blue-500 text-white"
                      : "bg-gray-200 text-gray-800"
                  }`}
                >
                  <p className="text-sm">{msg.content}</p>
                  <p
                    className={`text-xs mt-1 opacity-75 ${
                      msg.id_sender == user.id ? "text-right" : "text-left"
                    }`}
                  >
                    {timeAgo(msg.sent_at)}
                  </p>
                </div>
              </div>
            ))}
            <div ref={messagesEndRef} />
          </div>

          <form
            onSubmit={handleSendMessage}
            className="flex items-center p-2 border-t"
          >
            <input
              type="text"
              value={newMessage}
              onChange={(e) => setNewMessage(e.target.value)}
              placeholder="Digite uma mensagem..."
              className="flex-1 border rounded-full px-4 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <button
              type="submit"
              className="ml-2 text-blue-600 hover:text-blue-500 p-2 rounded-full"
            >
              <Send size={20} />
            </button>
          </form>
        </>
      )}
    </div>
  );
}
