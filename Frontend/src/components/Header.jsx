import React, { useState, useRef, useEffect } from 'react';
import { Link, useHistory } from 'react-router-dom';
import apiClient from '../api/axiosConfig';
import { Search, Bell, MessageCircle, PlusSquare, X } from 'lucide-react';
import { useUser } from '../contexts/UserContext';
import iconeProfile from '../images/iconeProfile.png';
import iconeLogo from '../images/logo.png';
import SearchDropdown from './SearchDropdown';

export default function Header() {
  const { user, logout, setIsChatBarVisible } = useUser();
  const history = useHistory();
  
  const [query, setQuery] = useState('');
  const [searchResults, setSearchResults] = useState(null);
  const [isSearchFocused, setIsSearchFocused] = useState(false);
  const searchContainerRef = useRef();

  const [showModal, setShowModal] = useState(false);
  const [showMenu, setShowMenu] = useState(false);
  const menuRef = useRef();
  const [notifications, setNotifications] = useState([]);
  const [showNotifications, setShowNotifications] = useState(false);
  const notificationRef = useRef();
  const [unreadMessageCount, setUnreadMessageCount] = useState(0);

  useEffect(() => {
    if (!user) return;

    const fetchBellNotifications = async () => {
      try {
        const { data } = await apiClient.get(`/notification/formatted/${user.id}`);
        setNotifications(data);
      } catch (error) {
        if (error.response?.status !== 401) {
          console.error("Falha ao buscar notificações do sino:", error);
        }
      }
    };

    const fetchMessageSummary = async () => {
      try {
        const { data } = await apiClient.get(`/direct-messages/unread-summary/${user.id}`);
        const totalUnread = data.reduce((sum, item) => sum + parseInt(item.unread_count, 10), 0);
        setUnreadMessageCount(totalUnread);
      } catch (error) {
        if (error.response?.status !== 401) {
          console.error("Falha ao buscar resumo de mensagens:", error);
        }
      }
    };

    fetchBellNotifications();
    fetchMessageSummary();

    const bellInterval = setInterval(fetchBellNotifications, 30000);
    const messageInterval = setInterval(fetchMessageSummary, 10000);

    return () => {
      clearInterval(bellInterval);
      clearInterval(messageInterval);
    };
  }, [user]);

  useEffect(() => {
    if (query.trim().length < 2) {
      setSearchResults(null);
      return;
    }
    const handler = setTimeout(() => {
      apiClient.get(`/search?q=${encodeURIComponent(query)}`)
        .then(res => setSearchResults(res.data))
        .catch(err => console.error("Erro na busca:", err));
    }, 300);
    return () => clearTimeout(handler);
  }, [query]);

  useEffect(() => {
    const handleClickOutside = (e) => {
      if (searchContainerRef.current && !searchContainerRef.current.contains(e.target)) {
        setIsSearchFocused(false);
      }
      if (menuRef.current && !menuRef.current.contains(e.target)) {
        setShowMenu(false);
      }
      if (notificationRef.current && !notificationRef.current.contains(e.target)) {
        setShowNotifications(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleSearchSubmit = (e) => {
    e.preventDefault();
    if (!query.trim()) return;
    const encodedQuery = encodeURIComponent(query);
    setQuery('');
    setSearchResults(null);
    setIsSearchFocused(false);
    history.push(`/search-results?q=${encodedQuery}`);
  };

  const handleResultClick = () => {
    setQuery('');
    setSearchResults(null);
    setIsSearchFocused(false);
  };
  
  const handleNotificationClick = async (notification) => {
    try {
      await apiClient.put(`/notification/clear/${notification.id}`);
    } catch (error) {
      console.error("Erro ao marcar notificação como lida:", error);
    }
    setNotifications(prev => prev.filter(n => n.id !== notification.id));
    setShowNotifications(false);

    const { target_type, target_id } = notification;

    if (!target_id) {
        console.error("Notificação sem target_id:", notification);
        return; 
    }

    if (target_type === 'post') {
      history.push(`/posts/${target_id}`);
    } else if (target_type === 'user') {
      history.push(`/users/${target_id}`);
    } else if (target_type === 'community') {
      history.push(`/communities/${target_id}`);
    } else {
        console.warn("Tipo de notificação desconhecido para redirecionamento:", target_type);
    }
    // -----------------------------------------------------------
  };

  return (
    <>
      <header className="flex items-center px-4 py-2 bg-white shadow relative">
        <Link to="/home">
          <img src={iconeLogo} alt="Logo" className="h-8" />
        </Link>
        
        <div ref={searchContainerRef} className="relative flex-1 mx-4">
          <form onSubmit={handleSearchSubmit}>
            <div className="flex">
              <input
                type="text"
                value={query}
                onChange={e => setQuery(e.target.value)}
                onFocus={() => setIsSearchFocused(true)}
                className="flex-1 border rounded-l px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Buscar por posts, r/comunidades ou u/usuários"
              />
              <button type="submit" className="bg-blue-500 text-white px-4 rounded-r hover:bg-blue-600">
                <Search size={20} />
              </button>
            </div>
          </form>
          {isSearchFocused && query && searchResults && (
            <SearchDropdown results={searchResults} onResultClick={handleResultClick} />
          )}
        </div>
        
        <nav className="flex items-center space-x-4">
          <div className="relative">
            <MessageCircle className="w-6 h-6 cursor-pointer" onClick={() => setIsChatBarVisible(prev => !prev)} />
            {unreadMessageCount > 0 && (
              <span className="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-xs text-white">
                {unreadMessageCount > 9 ? '9+' : unreadMessageCount}
              </span>
            )}
          </div>
          <PlusSquare className="w-6 h-6 cursor-pointer" onClick={() => setShowModal(true)} />
          <div className="relative" ref={notificationRef}>
            <Bell className="w-6 h-6 cursor-pointer" onClick={() => setShowNotifications(prev => !prev)} />
            {notifications.length > 0 && (
              <span className="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-xs text-white">
                {notifications.length > 9 ? '9+' : notifications.length}
              </span>
            )}
            {showNotifications && (
              <div className="absolute right-0 mt-2 w-80 bg-white border rounded shadow-lg z-50 max-h-96 overflow-y-auto">
                <div className="p-3 font-bold border-b">Notificações</div>
                {notifications.length > 0 ? (
                  notifications.map(n => <div key={n.id} onClick={() => handleNotificationClick(n)} className="px-3 py-2 text-sm hover:bg-gray-100 cursor-pointer border-b">{n.text}</div>)
                ) : (
                  <div className="px-3 py-4 text-sm text-gray-500 text-center">Nenhuma notificação nova.</div>
                )}
              </div>
            )}
          </div>
          <div className="relative" ref={menuRef}>
            <img src={iconeProfile} alt="Perfil" className="w-8 h-8 rounded-full cursor-pointer" onClick={() => setShowMenu(prev => !prev)} />
            {showMenu && (
              <div className="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg z-50">
                <button className="block w-full text-left px-4 py-2 hover:bg-gray-100" onClick={() => { setShowMenu(false); history.push(`/users/${user.id}`); }}>
                  Ver perfil
                </button>
                <button className="block w-full text-left px-4 py-2 hover:bg-gray-100" onClick={() => { setShowMenu(false); logout(); history.push('/login'); }}>
                  Sair
                </button>
              </div>
            )}
          </div>
        </nav>
      </header>
      
      {showModal && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg shadow-lg w-80 p-6 relative">
            <button className="absolute top-3 right-3 text-gray-500 hover:text-gray-700" onClick={() => setShowModal(false)}><X size={18} /></button>
            <h2 className="text-xl font-semibold mb-4">O que deseja criar?</h2>
            <div className="flex flex-col space-y-3">
              <button className="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500" onClick={() => { setShowModal(false); history.push('/post/create'); }}>Criar post</button>
              <button className="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500" onClick={() => { setShowModal(false); history.push('/community/create'); }}>Criar comunidade</button>
            </div>
          </div>
        </div>
      )}
    </>
  );
}