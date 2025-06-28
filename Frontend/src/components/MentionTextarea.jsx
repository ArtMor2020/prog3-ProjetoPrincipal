import React, { useState, useEffect, useRef } from "react";
import axios from "axios";
import apiClient from '../api/axiosConfig';

export default function MentionTextarea({
  value = "",
  onChange,
  placeholder,
  rows = 4,
}) {
  const [suggestions, setSuggestions] = useState([]);
  const [showSuggestions, setShowSuggestions] = useState(false);
  const textareaRef = useRef(null);

  useEffect(() => {
    if (typeof value !== "string") {
      setShowSuggestions(false);
      return;
    }

    const mentionMatch = value.match(/u\/(\w*)$/);
    if (mentionMatch) {
      const query = mentionMatch[1];
      setShowSuggestions(true);

      const handler = setTimeout(() => {
        apiClient
          .get(`http://localhost:8080/search/users`, {
            params: { term: query },
          })
          .then((res) => {
            setSuggestions(res.data || []);
          })
          .catch((err) => console.error("Erro na busca por menção:", err));
      }, 300);

      return () => clearTimeout(handler);
    } else {
      setShowSuggestions(false);
    }
  }, [value]);

  const handleSuggestionClick = (username) => {
    const newValue = value.replace(/u\/\w*$/, `u/${username} `);
    onChange(newValue);
    setShowSuggestions(false);
    textareaRef.current.focus();
  };

  return (
    <div className="relative">
      <textarea
        ref={textareaRef}
        value={value}
        onChange={(e) => onChange(e.target.value)}
        placeholder={placeholder}
        rows={rows}
        className="w-full border rounded px-3 py-2"
      />
      {showSuggestions && suggestions.length > 0 && (
        <div className="absolute z-10 w-full bg-white border rounded shadow-lg mt-1 max-h-40 overflow-y-auto">
          {suggestions.map((user) => (
            <div
              key={user.id}
              onClick={() => handleSuggestionClick(user.name)}
              className="px-3 py-2 cursor-pointer hover:bg-gray-100"
            >
              {user.name}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
