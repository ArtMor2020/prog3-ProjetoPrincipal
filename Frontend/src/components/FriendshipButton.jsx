import React, { useState, useEffect, useCallback } from "react";
import axios from "axios";
import { useUser } from "../contexts/UserContext";
import { UserPlus, UserCheck, Clock, UserX } from "lucide-react";

export default function FriendshipButton({ profileUserId }) {
  const { user } = useUser();
  const [friendshipStatus, setFriendshipStatus] = useState("loading");
  const [requestId, setRequestId] = useState(null);

  const checkFriendshipStatus = useCallback(async () => {
    if (!user) {
      setFriendshipStatus(null);
      return;
    }

    try {
      const { data } = await axios.get(
        `http://localhost:8080/friendship/status/${user.id}/${profileUserId}`
      );
      setFriendshipStatus(data.status);
      if (data.request_id) {
        setRequestId(data.request_id);
      }
    } catch (error) {
      console.error("Erro ao verificar status de amizade:", error);
      setFriendshipStatus("not_friends");
    }
  }, [user, profileUserId]);

  useEffect(() => {
    checkFriendshipStatus();
  }, [checkFriendshipStatus]);

  const handleSendRequest = async () => {
    setFriendshipStatus("loading");
    try {
      await axios.post("http://localhost:8080/friendship/send-request", {
        id_user1: user.id,
        id_user2: profileUserId,
      });
      setFriendshipStatus("request_sent");
    } catch (error) {
      console.error("Erro ao enviar pedido de amizade:", error);
      checkFriendshipStatus();
    }
  };

  const handleAcceptRequest = async () => {
    if (!requestId) return;
    setFriendshipStatus("loading");
    try {
      await axios.put(`http://localhost:8080/friendship/accept/${requestId}`);
      setFriendshipStatus("friends");
    } catch (error) {
      console.error("Erro ao aceitar pedido:", error);
      checkFriendshipStatus();
    }
  };

  const handleDeclineRequest = async () => {
    if (!requestId) return;
    setFriendshipStatus("loading");
    try {
      await axios.delete(
        `http://localhost:8080/friendship/refuse/${requestId}`
      );
      setFriendshipStatus("not_friends");
    } catch (error) {
      console.error("Erro ao recusar pedido:", error);
      checkFriendshipStatus();
    }
  };

  const renderButton = () => {
    switch (friendshipStatus) {
      case "not_friends":
        return (
          <button
            onClick={handleSendRequest}
            className="flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 transition"
          >
            <UserPlus size={18} className="mr-2" />
            Adicionar Amigo
          </button>
        );
      case "request_sent":
        return (
          <button
            disabled
            className="flex items-center px-4 py-2 bg-gray-400 text-white rounded cursor-not-allowed"
          >
            <Clock size={18} className="mr-2" />
            Pedido Enviado
          </button>
        );
      case "request_received":
        return (
          <div className="flex space-x-2">
            <button
              onClick={handleAcceptRequest}
              className="flex items-center px-4 py-2 bg-green-500 text-white rounded hover:bg-green-400 transition"
            >
              <UserCheck size={18} className="mr-2" />
              Aceitar
            </button>
            <button
              onClick={handleDeclineRequest}
              className="flex items-center px-4 py-2 bg-red-500 text-white rounded hover:bg-red-400 transition"
            >
              <UserX size={18} className="mr-2" />
              Recusar
            </button>
          </div>
        );
      case "friends":
        return (
          <button
            disabled
            className="flex items-center px-4 py-2 bg-gray-400 text-white rounded cursor-not-allowed"
          >
            <UserCheck size={18} className="mr-2" />
            Amigos
          </button>
        );
      case "loading":
        return <div className="px-4 py-2">Carregando...</div>;
      default:
        return null;
    }
  };

  return <div className="ml-4">{renderButton()}</div>;
}
