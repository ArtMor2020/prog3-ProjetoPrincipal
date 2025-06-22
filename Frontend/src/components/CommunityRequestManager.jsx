import React, { useState, useEffect, useCallback } from "react";
import axios from "axios";
import { useUser } from "../contexts/UserContext";

const RequestItem = ({ request, onAction }) => {
  const [communityName, setCommunityName] = useState("...");

  useEffect(() => {
    axios
      .get(`http://localhost:8080/communities/${request.id_community}`)
      .then((res) => setCommunityName(res.data.name))
      .catch(console.error);
  }, [request.id_community]);

  const handleAction = async (action) => {
    const url = `http://localhost:8080/community-join-requests/${request.id}/${action}`;
    try {
      await axios.put(url);
      onAction();
    } catch (error) {
      console.error(`Erro ao ${action} o pedido:`, error);
    }
  };

  return (
    <div className="p-3 border-b bg-gray-50 rounded-md">
      <p className="text-sm">
        Pedido para entrar na comunidade:{" "}
        <span className="font-bold">r/{communityName}</span>
      </p>
      <div className="flex space-x-2 mt-2">
        <button
          onClick={() => handleAction("approve")}
          className="px-3 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600"
        >
          Aceitar
        </button>
        <button
          onClick={() => handleAction("reject")}
          className="px-3 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600"
        >
          Recusar
        </button>
      </div>
    </div>
  );
};

export default function CommunityRequestManager({ profileUserId }) {
  const { user } = useUser();
  const [requests, setRequests] = useState([]);

  const fetchRequests = useCallback(async () => {
    if (!user) return;
    try {
      const { data: pendingRequests } = await axios.get(
        `http://localhost:8080/community-join-requests/user/${profileUserId}`
      );
      const filteredPending = pendingRequests.filter(
        (r) => r.status === "pending"
      );

      const { data: userAdminOf } = await axios.get(
        `http://localhost:8080/user-communities/user/${user.id}`
      );
      const adminOfCommunityIds = userAdminOf
        .filter((uc) => ["ADMIN", "MODERATOR"].includes(uc.role))
        .map((uc) => String(uc.id_community));

      const manageableRequests = filteredPending.filter((req) =>
        adminOfCommunityIds.includes(String(req.id_community))
      );
      setRequests(manageableRequests);
    } catch (error) {
      console.error("Erro ao buscar pedidos gerenciáveis:", error);
    }
  }, [user, profileUserId]);

  useEffect(() => {
    fetchRequests();
  }, [fetchRequests]);

  if (requests.length === 0) return null;

  return (
    <div className="bg-white shadow-sm rounded p-4 mt-4 border-t-4 border-blue-500">
      <h3 className="font-bold mb-2">Pedidos Pendentes de Comunidade</h3>
      <div className="space-y-2">
        {requests.map((req) => (
          <RequestItem key={req.id} request={req} onAction={fetchRequests} />
        ))}
      </div>
    </div>
  );
}
