import React, { useState, useEffect, useCallback } from 'react';
import { Link } from 'react-router-dom';
import apiClient from '../api/axiosConfig';

const RequestItem = ({ request, onAction }) => {
  const [userName, setUserName] = useState('...');

  useEffect(() => {
    apiClient.get(`/users/${request.id_user}`)
      .then(res => setUserName(res.data.name))
      .catch(err => {
        console.error(`Erro ao buscar usuário ${request.id_user}`, err);
        setUserName('Usuário Desconhecido');
      });
  }, [request.id_user]);

  const handleAction = async (action) => {
    try {
      await apiClient.put(`/community-join-requests/${request.id}/${action}`);
      onAction(); 
    } catch (error) {
      console.error(`Erro ao ${action} o pedido:`, error);
      alert('Não foi possível processar a ação.');
    }
  };

  return (
    <div className="flex justify-between items-center p-3 border-b bg-gray-50 last:border-b-0">
      <Link to={`/users/${request.id_user}`} className="font-semibold text-blue-600 hover:underline">
        u/{userName}
      </Link>
      <div className="flex space-x-2">
        <button onClick={() => handleAction('approve')} className="px-3 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">Aceitar</button>
        <button onClick={() => handleAction('reject')} className="px-3 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">Recusar</button>
      </div>
    </div>
  );
};

export default function JoinRequestManager({ communityId, isModerator }) {
  const [requests, setRequests] = useState([]);
  const [loading, setLoading] = useState(true);

  const fetchRequests = useCallback(async () => {
    try {
      const { data } = await apiClient.get(`/community-join-requests/community/${communityId}`);
      const pendingRequests = data.filter(r => r.status === 'pending');
      setRequests(pendingRequests);
    } catch (error) {
      console.error("Erro ao buscar pedidos de ingresso:", error);
    } finally {
      setLoading(false);
    }
  }, [communityId]);

  useEffect(() => {
    if (isModerator) {
      fetchRequests();
    }
  }, [isModerator, fetchRequests]);

  if (!isModerator || (loading === false && requests.length === 0)) {
    return null;
  }

  return (
    <div className="bg-white shadow-sm rounded-lg my-6">
      <h3 className="font-bold p-4 border-b text-lg">Pedidos para Entrar</h3>
      {loading ? (
        <p className="p-4 text-gray-500">Carregando pedidos...</p>
      ) : (
        <div className="space-y-2">
          {requests.map(req => (
            <RequestItem key={req.id} request={req} onAction={fetchRequests} />
          ))}
        </div>
      )}
    </div>
  );
}