## ⚙️ 1 Fluxo de Git

Crie uma branch a partir da main:

git checkout -b feat/nome-da-feature

Siga o padrão de commits (Conventional Commits):
feat: nova funcionalidade
fix: correção de bug
docs: atualização de documentação
refactor: refatoração de código
chore: manutenção
style: ajustes de estilo
test: testes automatizados


Envie seu trabalho:
git add .
git commit -m "feat: descrição objetiva"
git push origin feat/nome-da-feature


## 💻 2 Padrões de Código

PHP: manter scripts em php/ e usar include 'conexao.php';

HTML/CSS: arquivos em web/ e css/

SQL: dentro de banco/ (schema.sql, seed.sql)

Usar nomes claros e significativos

Nunca incluir credenciais no código (se necessário, usar .env)

## 🐞 3 Issues

Utilize os templates de bug e feature disponíveis.

Inclua:

Passos para reproduzir

Prints ou logs

Contexto adicional (navegador, sistema, etc.)

## 🔒 4 Segurança

Não publique dados sensíveis.

Vulnerabilidades devem ser reportadas de forma privada
→ consulte o arquivo SECURITY.md

## 🧠 5 Decisões de Projeto

Modelos conceitual e lógico → pasta docs/

Modelo físico e scripts SQL → pasta banco/

Backend em PHP puro (sem framework), com fins acadêmicos

☕ Agradecimentos

Agradecemos sua colaboração!
Cada contribuição ajuda o Café Moderno a evoluir com qualidade e propósito.