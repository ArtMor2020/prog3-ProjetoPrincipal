import React, { useState, useEffect, useCallback } from 'react';
import { useUser } from '../contexts/UserContext';
import { ShieldOff, Shield } from 'lucide-react';
import apiClient from '../api/axiosConfig'; 

export default function BlockUserButton({ profileUserId, onBlockStatusChange }) {
  const { user } = useUser();
  const [isBlocked, setIsBlocked] = useState(false);
  const [loading, setLoading] = useState(true);

  const checkBlockStatus = useCallback(async () => {
    if (!user || String(user.id) === String(profileUserId)) {
      setLoading(false);
      return;
    }
    try {
      const { data } = await apiClient.get(`/blocked-users/${user.id}`);
      const isUserBlocked = data.some(b => String(b.id_blocked_user) === String(profileUserId));
      setIsBlocked(isUserBlocked);
    } catch (error) {
      if (error.response?.status !== 401) {
        console.error("Erro ao verificar status de bloqueio:", error);
      }
    } finally {
      setLoading(false);
    }
  }, [user, profileUserId]);

  useEffect(() => {
    checkBlockStatus();
  }, [checkBlockStatus]);

  const handleToggleBlock = async () => {
    setLoading(true);
    const action = isBlocked ? 'unblock' : 'block';
    const payload = {
      id_user: user.id,
      id_blocked_user: profileUserId
    };
    const url = isBlocked ? '/blocked-users/unblock' : '/blocked-users/block';
    const method = isBlocked ? 'put' : 'post';

    try {
      await apiClient[method](url, payload);
      const newBlockStatus = !isBlocked;
      setIsBlocked(newBlockStatus);
      if (onBlockStatusChange) onBlockStatusChange(newBlockStatus);
    } catch (error) {
      if (error.response?.status !== 401) {
        console.error(`Erro ao ${action} usuário:`, error);
      }
    } finally {
      setLoading(false);
    }
  };

  if (!user || String(user.id) === String(profileUserId)) {
      return null; 
  }

  if (loading) return <div className="text-sm text-gray-500 px-3 py-1">...</div>;

  return (
    <button onClick={handleToggleBlock} className={`flex items-center px-3 py-1 text-sm rounded transition ${isBlocked ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-red-600 hover:bg-red-700 text-white'}`}>
      {isBlocked ? <Shield size={16} className="mr-2" /> : <ShieldOff size={16} className="mr-2" />}
      {isBlocked ? 'Desbloquear' : 'Bloquear'}
    </button>
  );
}