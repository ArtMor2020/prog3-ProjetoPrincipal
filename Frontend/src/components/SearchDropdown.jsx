import React from "react";
import { Link } from "react-router-dom";
import { Hash, Users, MessageSquare } from "lucide-react";

const ResultItem = ({ to, icon, title, subtitle }) => (
  <Link to={to} className="flex items-center p-2 hover:bg-gray-100 rounded-md">
    <div className="mr-3 text-gray-500">{icon}</div>
    <div>
      <div className="font-semibold text-sm">{title}</div>
      {subtitle && <div className="text-xs text-gray-500">{subtitle}</div>}
    </div>
  </Link>
);

export default function SearchDropdown({ results, onResultClick }) {
  const hasResults =
    results.communities.length > 0 ||
    results.users.length > 0 ||
    results.posts.length > 0;

  if (!hasResults) {
    return (
      <div className="absolute top-full mt-2 w-full bg-white border rounded shadow-lg p-3 text-center text-sm text-gray-500">
        Nenhum resultado encontrado.
      </div>
    );
  }

  return (
    <div
      onClick={onResultClick}
      className="absolute top-full mt-2 w-full bg-white border rounded shadow-lg p-2 max-h-96 overflow-y-auto"
    >
      {results.communities.length > 0 && (
        <div className="mb-2">
          <h3 className="px-2 text-xs font-bold text-gray-400 uppercase">
            Comunidades
          </h3>
          {results.communities.slice(0, 3).map((c) => (
            <ResultItem
              key={`c-${c.id}`}
              to={`/communities/${c.id}`}
              icon={<Users size={18} />}
              title={`r/${c.name}`}
            />
          ))}
        </div>
      )}
      {results.users.length > 0 && (
        <div className="mb-2">
          <h3 className="px-2 text-xs font-bold text-gray-400 uppercase">
            Usuários
          </h3>
          {results.users.slice(0, 3).map((u) => (
            <ResultItem
              key={`u-${u.id}`}
              to={`/users/${u.id}`}
              icon={<Users size={18} />}
              title={`u/${u.name}`}
            />
          ))}
        </div>
      )}
      {results.posts.length > 0 && (
        <div>
          <h3 className="px-2 text-xs font-bold text-gray-400 uppercase">
            Posts
          </h3>
          {results.posts.slice(0, 5).map((p) => (
            <ResultItem
              key={`p-${p.id}`}
              to={`/posts/${p.id}`}
              icon={<MessageSquare size={18} />}
              title={p.title}
              subtitle={`em r/${p.community?.name || "perfil"}`}
            />
          ))}
        </div>
      )}
    </div>
  );
}
