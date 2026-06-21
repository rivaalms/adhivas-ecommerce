FROM node:20-alpine AS builder

# Install pnpm
RUN npm install -g pnpm

# Set working directory
WORKDIR /app

# Copy lockfile and package.json
COPY package.json pnpm-lock.yaml* ./

# Install dependencies
RUN pnpm install --frozen-lockfile

# Copy source files
COPY . .

# Build Nuxt application
RUN pnpm run build

# Production runner stage
FROM node:20-alpine

WORKDIR /app

# Copy built application from builder stage
COPY --from=builder /app/.output ./.output
COPY --from=builder /app/package.json ./package.json

# Expose Nuxt port
EXPOSE 3000

# Set production env vars
ENV PORT=3000
ENV HOST=0.0.0.0

# Start Nuxt server
CMD ["node", ".output/server/index.mjs"]
