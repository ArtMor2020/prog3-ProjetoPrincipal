import React, { useState, useEffect, useCallback } from "react";
import axios from "axios";
import { useUser } from "../contexts/UserContext";
import { LogIn, Check, Clock } from "lucide-react";
import apiClient from '../api/axiosConfig';

export default function CommunityJoinButton({ communityId }) {
  const { user } = useUser();
  const [status, setStatus] = useState("loading");

  const checkStatus = useCallback(async () => {
    if (!user) {
      setStatus("not_member");
      return;
    }
    try {
      const { data } = await apiClient.get(
        `http://localhost:8080/communities/user-status/${communityId}/${user.id}`
      );
      setStatus(data.status);
    } catch (error) {
      console.error("Erro ao verificar status da comunidade:", error);
      setStatus("not_member");
    }
  }, [user, communityId]);

  useEffect(() => {
    checkStatus();
  }, [checkStatus]);

  const handleJoinRequest = async () => {
    setStatus("loading");
    try {
      await apiClient.post("http://localhost:8080/community-join-requests", {
        community_id: communityId,
        user_id: user.id,
      });
      setStatus("request_sent");
    } catch (error) {
      console.error("Erro ao enviar pedido:", error);
      checkStatus();
    }
  };

  switch (status) {
    case "not_member":
      return (
        <button
          onClick={handleJoinRequest}
          className="flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 transition"
        >
          <LogIn size={18} className="mr-2" />
          Entrar na Comunidade
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
    case "member":
    case "ADMIN":
    case "MODERATOR":
      return (
        <button
          disabled
          className="flex items-center px-4 py-2 bg-green-600 text-white rounded cursor-not-allowed"
        >
          <Check size={18} className="mr-2" />
          Membro
        </button>
      );
    default:
      return <div className="px-4 py-2">Carregando...</div>;
  }
}
